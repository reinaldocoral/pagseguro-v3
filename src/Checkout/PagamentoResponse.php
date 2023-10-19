<?php

namespace ReinaldoCoral\Pagseguro\Checkout;

use ReinaldoCoral\Pagseguro\Domains\HttpResponse;

class PagamentoResponse extends HttpResponse
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

    public function getPagamentoUrl()
    {
        $result = array_filter($this->object()->links ?? [], function($item) {
            return $item->rel === 'PAY';
        });
        
        if (!empty($result)) {
            $firstItem = reset($result);
            $href = $firstItem->href;
            return $href;
        }
        return null;
    }

}





