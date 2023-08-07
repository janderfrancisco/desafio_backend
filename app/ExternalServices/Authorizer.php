<?php

namespace App\ExternalServices;

use Illuminate\Support\Facades\Http;

class Authorizer
{

    private $base_url = '';

    public function __construct()
    {
        $this->base_url = config('authorizer.base_url');
    }

    /**
     * authorizeTtransaction
     *
     * Função responsável por chamar o serviço de autorização de transação
     * cada transação deve ser autorizada antes de ser efetivada
     * a função tenta 3 vezes com intervalo de 250ms. caso não consiga, retorna false
     *
     * @return bool
     */
    public function authorizeTransaction()
    {
        $response = Http::retry(3,250)->timeout(2)->get($this->base_url);

        // if response status
        if ($response->status() == 200){

            if ($response->json()['message'] == 'Autorizado')
                return true;
        }

        return false;
    }
}
