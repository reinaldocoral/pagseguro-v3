<?php

namespace ReinaldoCoral\Pagseguro\Connect;

use GuzzleHttp\Client;
use ReinaldoCoral\Pagseguro\Configure;
use ReinaldoCoral\Pagseguro\Services\LogService;

class ConnectRequest
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

    public function executeGetToken(Configure $config, $headers = [])
    {
        return $this->execute($config, $headers, '/oauth2/token');
    }

    public function executeGenerateCertified(Configure $config, $headers = [])
    {
        return $this->execute($config, $headers, '/public-keys');
    }

    private function execute(Configure $config, $headers = [], $endpoint = '')
    {
        $response = $this->http_client->request('POST', $endpoint, [
            'base_uri' => $config->getEndpointBase(),
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
            ], $headers),
            'body'    => json_encode($this->payload),
        ]);

        return new ConnectResponse($response);
    }
}
