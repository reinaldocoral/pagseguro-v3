<?php

namespace ReinaldoCoral\Pagseguro\Domains;

class HttpResponse
{
    /**
     * The underlying PSR response.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;
    protected $decoded;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function successful()
    {
        return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
    }

    public function body()
    {
        return (string) $this->response->getBody();
    }

    public function json()
    {
        if (! $this->decoded) {
            $this->decoded = json_decode($this->body(), true);
        }

        return $this->decoded;
    }

    public function object()
    {
        return json_decode($this->body(), false);
    }
}





