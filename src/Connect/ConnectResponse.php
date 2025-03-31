<?php

namespace ReinaldoCoral\Pagseguro\Connect;

use ReinaldoCoral\Pagseguro\Domains\HttpResponse;

class ConnectResponse extends HttpResponse
{

    public function __construct($response)
    {
        parent::__construct($response);
    }

    public function getPayload()
    {
        return $this->body();
    }

    public function getCode()
    {
        return $this->successful() ? $this->object()->id : null;
    }

}





