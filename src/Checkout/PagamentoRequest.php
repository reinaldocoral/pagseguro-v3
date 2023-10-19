<?php

namespace ReinaldoCoral\Pagseguro\Checkout;

use GuzzleHttp\Client;
use ReinaldoCoral\Pagseguro\Configure;

class PagamentoRequest
{
    private $payload;

    public function __construct()
    {
        
    }

    public function setPayload( $payload )
    {
        $this->payload = $payload;
    }

    public function execute(Configure $config)
    {
        $http_client = new Client(['base_uri' => $config->getEndpointBase()]);
        $response = $http_client->request('POST', '/checkouts', [
            'headers' => [
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
                'x-idempotency-key' => ''
            ],
            'body'    => json_encode($this->payload),
        ]);

        return new PagamentoResponse($response);
    }
}
