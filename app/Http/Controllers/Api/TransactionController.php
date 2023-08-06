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
        $transactions = Transaction::with('payer', 'payee')->get();
        return response()->json($transactions, 200);
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
        $payer = User::where('id', $data['payer_id'])->first();
        $payee = User::where('id', $data['payee_id'])->first();
        echo '<pre>';
            print_r($authorizer);
        echo '</pre>';

        // se não autorizado, retorna erro
        if(!$authorizer['message'] == 'Autorizado')
            return response()->json(['message' => 'Transação não autorizada pelo agente externo'], 401);
        
        // se não tiver saldo, retorna erro
        if($data['value'] > $payee->balance)
            return response()->json(['message' => 'Saldo insuficiente'], 401);
         
        $this->removeBalance($payer, $data['value']);
        $this->addBalance($payee, $data['value']);
        // registra a transação
        $transaction = Transaction::create($data);
        // envia notificação
        $notifier = $this->notifier->notifyTransaction();

        // se falhar na notificação, reverte a transação
        if(!$notifier['message'] == 'Enviado'){

            $this->addBalance($payer, $data['value']);
            $this->removeBalance($payee, $data['value']);
            $transaction->delete();

            return response()->json(['message' => 'Transação não realizada, tente novamente'], 401);
        }

        return response()->json(['message' => 'Transação realizada com sucesso'], 200);
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

        $transaction = Transaction::where('uuid', $data['uuid'])->with('payee', 'payer')->first();

        if(!$transaction)
            return response()->json(['message' => 'Transação não encontrada'], 404);
        
        $this->addBalance($transaction->payer, $transaction->value);
        $this->removeBalance($transaction->payee, $transaction->value);
         
        // envia notificação
        $notifier = $this->notifier->notifyTransaction();

        // se falhar na notificação, reverte a transação
        if(!$notifier['message'] == 'Enviado'){

            $this->removeBalance($transaction->payer, $transaction->value);
            $this->addBalance($transaction->payee, $transaction->value);
 
            return response()->json(['message' => 'Transação não realizada, tente novamente'], 401);
        }

        $transaction->save();
 
        return response()->json(['message' => 'Transação revertida com sucesso'], 200);
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
        $user->balance = $user->balance + $value;
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
        $user->balance = $user->balance - $value;
        $user->save();
    }

}
