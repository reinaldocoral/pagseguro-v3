<?php

namespace ReinaldoCoral\Pagseguro\Connect;

use GuzzleHttp\Client;
use ReinaldoCoral\Pagseguro\Configure;

class ConnectRequest
{
    private $payload;

    public function __construct()
    {
        
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
        $http_client = new Client(['base_uri' => $config->getEndpointBase()]);
        $response = $http_client->request('POST', $endpoint, [
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
                // 'x-idempotency-key' => ''
            ], $headers),
            'body'    => json_encode($this->payload),
        ]);

        return new ConnectResponse($response);
    }
}
