<?php

namespace App\ExternalServices;

use Illuminate\Support\Facades\Http;

class Notifier
{

    private $base_url = '';

    public function __construct()
    {
        $this->base_url = config('notifier.base_url');
    }

    /**
     * authorizeTtransaction
     * Função responsável por enviar notificações das transações
     *  a função tem timeout de 2 segundos. caso não consiga, retorna false
     * @return bool
     */
    public function notification()
    {

        try {
            $response = Http::timeout(2)->get($this->base_url, []);
        } catch (\Exception $e) {
            return false;
        }

        if ($response->failed()) 
            return false;
        
        if ($response->status() == 200){
            if ($response->json()['message'] == 'Enviado')
                return true;
        }

        return false;
    }
}
