<?php

namespace ReinaldoCoral\Pagseguro\Checkout;

use ReinaldoCoral\Pagseguro\Configure;
use ReinaldoCoral\Pagseguro\Services\LogService;

class PagamentoRequest
{
    private $payload;
    private $http_client;

    public function __construct(LogService $logService, bool $enableLog = false)
    {
        $this->http_client = $logService->createClient($enableLog);
    }

    public function setPayload( $payload )
    {
        $this->payload = $payload;
    }

    public function execute(Configure $config, $headers = [])
    {
        $response = $this->http_client->request('POST', '/checkouts', [
            'base_uri' => $config->getEndpointBase(),
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
                // 'x-idempotency-key' => ''
            ], $headers),
            'body'    => json_encode($this->payload),
        ]);

        return new PagamentoResponse($response);
    }
}
