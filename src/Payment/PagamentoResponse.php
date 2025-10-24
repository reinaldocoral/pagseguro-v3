<?php

namespace ReinaldoCoral\Pagseguro\Payment;

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

    public function getOrderCode()
    {
        return $this->getCode();
    }

    public function getPagamentoCode()
    {
        if( $charge = $this->getLastCharge() ){
            return $charge->id;
        }

        if( $qr_codes = $this->getQRCodes() ){
            return $qr_codes[0]->id ?? null;
        }        

        return null;
    }

    public function getOrderPagamentoStatus()
    {
        if( $charge = $this->getLastCharge() ){
            return $charge->status;
        }       

        return null;
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

    private function isBoleto()
    {
        if( $charge = $this->getLastCharge() ){
            return $charge->payment_method->type === 'BOLETO';
        }
        return false;
    }

    public function getLastCharge()
    {
        if( $charges = $this->object()->charges ?? [] ){
            return $charges[ count($charges) - 1 ];
        }
        return null;
    }

    public function getBoletoPdfUrl()
    {
        if( !$this->isBoleto() ){
            return null;
        }

        $charge = $this->getLastCharge();
        $result = array_filter($charge->links ?? [], function($item) {
            return $item->media === 'application/pdf';
        });
        
        if (empty($result)) {
            return null;
        }

        $firstItem = reset($result);
        $href = $firstItem->href;
        return $href;
    }

    public function getBoletoBarcode()
    {
        if( !$this->isBoleto() ){
            return null;
        }

        $charge = $this->getLastCharge();
        return (object) [
            'barcode' => $charge->payment_method->boleto->barcode ?? null,
            'formatted_barcode' => $charge->payment_method->boleto->formatted_barcode ?? null,
        ];
    }

    public function getBoletoExpirationDate()
    {
        if( !$this->isBoleto() ){
            return null;
        }

        $charge = $this->getLastCharge();
        return $charge->payment_method->boleto->due_date;
    }

    public function getBoletoValor()
    {
        if( !$this->isBoleto() ){
            return null;
        }

        $charge = $this->getLastCharge();
        return $charge->amount->value;
    }

    private function getQRCodes()
    {
        return $this->object()->qr_codes ?? null;
    }

    public function getPixExpirationDate()
    {
        if( ! $qr_codes = $this->getQRCodes() ){
            return null;
        }

        return $qr_codes[0]->expiration_date ?? null;
    }

    public function getPixCopiaCola()
    {
        if( ! $qr_codes = $this->getQRCodes() ){
            return null;
        }

        return $qr_codes[0]->text ?? null;
    }

    public function getPixQrCodePng()
    {
        if( ! $qr_codes = $this->getQRCodes() ){
            return null;
        }

        $result = array_filter($qr_codes[0]->links ?? [], function($item) {
            return $item->rel === 'QRCODE.PNG';
        });
        
        if (empty($result)) {
            return null;
        }

        $firstItem = reset($result);
        $href = $firstItem->href;
        return $href;
    }
    
    public function getPixQrCodeBase64()
    {
        if( ! $qr_codes = $this->getQRCodes() ){
            return null;
        }

        $result = array_filter($qr_codes[0]->links ?? [], function($item) {
            return $item->rel === 'QRCODE.BASE64';
        });
        
        if (empty($result)) {
            return null;
        }

        $firstItem = reset($result);
        $href = file_get_contents($firstItem->href);
        return $href;
    }
}





