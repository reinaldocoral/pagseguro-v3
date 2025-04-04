<?php

namespace ReinaldoCoral\Pagseguro\Services;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LogService
{
    public function createClient(bool $enableLog = true)
    {
        $stack = HandlerStack::create();

        if ($enableLog) {
            $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
                $this->logRequest($request);
                return $request;
            }));

            $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
                $this->logResponse($response);
                return $response;
            }));
        }

        return new Client(['handler' => $stack]);
    }

    private function logRequest(RequestInterface $request)
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $headers = $request->getHeaders();
        $body = $request->getBody()->getContents();

        $logMessage = sprintf(
            "Request: %s %s\nHeaders: %s\nBody: %s\n",
            $method,
            $uri,
            json_encode($headers),
            $body
        );

        // error_log($logMessage);
        Storage::append('logs/pagseguro_' . date('Ymd') . '.txt', $logMessage);
    }

    private function logResponse(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();
        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();

        $logMessage = sprintf(
            "Response: %d\nHeaders: %s\nBody: %s\n",
            $statusCode,
            json_encode($headers),
            $body
        );

        // error_log($logMessage);
        Storage::append('logs/pagseguro_' . date('Ymd') . '.txt', $logMessage);
    }
}