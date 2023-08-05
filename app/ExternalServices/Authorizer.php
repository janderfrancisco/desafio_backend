<?php

namespace App\ExternalServices;

use Illuminate\Support\Facades\Http;

class Authorizer
{

    private $base_url = '';

    public function __construct()
    {
        $this->base_url = config('services.authorizer.base_url');
    }

    /**
     * authorizeTtransaction
     *
     * @return bool
     */
    public function authorizeTtransaction()
    {

        $response = Http::get($this->base_url, []);

        echo '<pre>';
            print_r($response->json());
        echo '</pre>';

    }
}
