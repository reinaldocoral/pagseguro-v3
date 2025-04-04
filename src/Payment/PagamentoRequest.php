<?php

namespace ReinaldoCoral\Pagseguro\Payment;

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
        $response = $this->http_client->request('POST', '/orders', [
            'base_uri' => $config->getEndpointBase(),
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
            ], $headers),
            'body'    => json_encode($this->payload),
        ]);

        return new PagamentoResponse($response);
    }

    public function executeGetInstallments(Configure $config, $headers = [])
    {
        $response = $this->http_client->request('GET', '/charges/fees/calculate', [
            'base_uri' => $config->getEndpointBase(),
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
            ], $headers),
            'query'    => $this->payload,
        ]);

        return new PagamentoResponse($response);
    }
}
