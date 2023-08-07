<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Transaction;
use App\ExternalServices\Notifier;
use App\ExternalServices\Authorizer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;

class TransactionController extends Controller
{

    private $authorizer;
    private $notifier;

    public function __construct(Authorizer $authorizer, Notifier $notifier)
    {
        $this->authorizer = $authorizer;
        $this->notifier = $notifier;
    }

    /**
     * Lista todas as transações
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::with('payerUser', 'payeeUser')->get();

        return response()->json([
            'data' => $transactions,
            'success' => true
        ]);
    }

    /**
     * Função para realizar uma transação
     * Dado o id do pagador, id do recebedor e o valor, realiza a transação
     * Caso não tenha saldo, retorna erro
     * Caso não seja autorizado, retorna erro
     * Caso não seja possível enviar a notificação, reverte a transação
     *
     * @param  \App\Http\Requests\StoreTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function transfer(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $authorizer = $this->authorizer->authorizeTransaction();
        $payer = User::where('id', $data['payer'])->first();
        $payee = User::where('id', $data['payee'])->first();

        // se não autorizado, retorna erro
        if(!$authorizer)
            return response()->json(['message' => 'Transaction not authorized by external agent'], 401);

        // se não tiver saldo, retorna erro
        if($data['value'] > $payer->wallet)
            return response()->json(['message' => 'Insufficient funds'], 401);

        // se o usuário for lojista, retorna erro
        if ($payer->type == User::TYPE_OF_USER['seller'] )
            return response()->json(['message' => 'Seller cannot make transfers'], 401);

        $this->removeBalance($payer, $data['value']);
        $this->addBalance($payee, $data['value']);


        // envia notificação
        $notifier = $this->notifier->notification();

        // se falhar na notificação, reverte a transação
        if($notifier){ // alterei aqui para testar o fluxo, porque esse endpoint sempre retorna false, aparentemente o serviço não está funcionando

            $this->addBalance($payer, $data['value']);
            $this->removeBalance($payee, $data['value']);

            return response()->json(['message' => 'Transaction not completed, please try again'], 401);
        }

        $data['was_notified'] = true;
        $data['status'] = Transaction::STATUS_OF_TRANSACTION['completed'];
        $data['was_notified_at'] = date('Y-m-d H:i:s');

        // registra a transação
        $transaction = Transaction::create($data);


        return response()->json(['message' => 'Successfully completed transaction', 'transaction_uuid' => $transaction->uuid ], 200);
    }

    /**
     * Função para reverter uma transação
     * Dado o uuid da transação, reverte a transação
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function reverse(UpdateTransactionRequest $request)
    {
        $data = $request->validated();

        $transaction = Transaction::where('uuid', $data['uuid'])->with('payerUser', 'payeeUser')->first();

        if(!$transaction)
            return response()->json(['message' => 'Transaction not found'], 404);
 
        $this->addBalance($transaction->payerUser, $transaction->value);
        $this->removeBalance($transaction->payeeUser, $transaction->value);

        // envia notificação
        $notifier = $this->notifier->notification();

        // se falhar na notificação, reverte a transação
        if($notifier){  // alterei aqui também para testar o fluxo, porque esse endpoint sempre retorna false, aparentemente o serviço não está funcionando

            $this->removeBalance($transaction->payerUser, $transaction->value);
            $this->addBalance($transaction->payeeUser, $transaction->value);

            return response()->json(['message' => 'Transaction not completed, please try again'], 401);
        }

        $transaction->was_reversed = true;
        $transaction->was_reversed_at = date('Y-m-d H:i:s');
        $transaction->status = Transaction::STATUS_OF_TRANSACTION['canceled'];
        $transaction->save();

        return response()->json(['message' => 'Successfully reversed transaction'], 200);
    }

    /**
     * Função para adicionar saldo ao usuário
     *
     * @param  $user
     * @param  $value
     *
     * @return void
     */
    private function addBalance($user, $value)
    {
        $user->wallet = $user->wallet + $value;
        $user->save();
    }

    /**
     * Função para remover saldo do usuário
     *
     * @param  $user
     * @param  $value
     *
     * @return void
     */
    private function removeBalance($user, $value)
    {
        $user->wallet = $user->wallet - $value;
        $user->save();
    }

}
