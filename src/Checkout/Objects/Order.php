<?php

namespace ReinaldoCoral\Pagseguro\Checkout\Objects;

class Order {
    private string $id;
    private string $reference_id;
    private Customer $customer;
    private $shipping;
    private $items;
    private $charges;
    private $qrcodes;
    private $links;
    private $notification_urls;
    private $order_status;
    private $created_at;

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->getId();
    }

    public function setReference($value)
    {
        $this->reference_id = $value;
    }

    public function getReference()
    {
        return $this->reference_id;
    }

    public function setDate($value)
    {
        $this->created_at = $value;
    }

    public function getDate()
    {
        return $this->created_at;
    }

    public function setCustomer(Customer $value)
    {
        $this->customer = $value;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setShipping($value)
    {
        $this->shipping = $value;
    }

    public function getShipping()
    {
        return $this->shipping;
    }

    public function addItems($value)
    {
        $this->items[] = $value;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setCharges($value)
    {
        $this->charges[] = $value;
    }

    public function getCharges()
    {
        return $this->charges;
    }

    public function setQrCodes($value)
    {
        $this->qrcodes[] = $value;
    }

    public function getQrCodes()
    {
        return $this->qrcodes;
    }

    public function setLinks($value)
    {
        $this->links[] = $value;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function setNotificationUrls($value)
    {
        $this->notification_urls[] = $value;
    }

    public function getNotificationUrls()
    {
        return $this->notification_urls;
    }

    public function setOrderStatus($value)
    {
        $this->order_status = $value;
    }

    public function getOrderStatus()
    {
        return $this->order_status;
    }

    public function getInstallmentCount()
    {
        return $this->getCharges()[0]['payment_method']['installments'] ?? null;
    }

    public function getPaymentDate()
    {
        return $this->getCharges()[0]['paid_at'] ?? null;
    }

    public function getPaymentMethodType()
    {
        return $this->getCharges()[0]['payment_method']['type'] ?? null;
    }
    
    public function getPaymentMethodCode()
    {
        return null;
    }

    public function getCancelationSource()
    {
        return null;
    }

    public function getGrossAmount()
    {
        return $this->getCharges()[0]['amount']['value'] ?? null;
    }

    public function getNetAmount()
    {
        return $this->getGrossAmount();
    }

    public function getDiscountAmount()
    {
        return null;
    }

    public function getExtraAmount()
    {
        return null;
    }

    public function getFeeAmount()
    {
        return null;
    }

    public function getEscrowEndDate()
    {
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
        if( $charges = $this->getCharges() ?? [] ){
            return json_decode( json_encode( $charges[ count($charges) - 1 ] ), false );
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
            // return null;
            return (object) [
                'barcode' => null,
                'formatted_barcode' => null,
            ];
        }

        $charge = $this->getLastCharge();
        return (object) [
            'barcode' => $charge->payment_method->boleto->barcode,
            'formatted_barcode' => $charge->payment_method->boleto->formatted_barcode,
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

    private function getQrCodesObject()
    {
        return json_decode(json_encode($this->getQrCodes()), false);
    }

    public function getPixExpirationDate()
    {
        if( ! $qr_codes = $this->getQrCodesObject() ){
            return null;
        }

        return $qr_codes[0]->expiration_date ?? null;
    }

    public function getPixCopiaCola()
    {
        if( ! $qr_codes = $this->getQrCodesObject() ){
            return null;
        }

        return $qr_codes[0]->text ?? null;
    }

    public function getPixQrCodePng()
    {
        if( ! $qr_codes = $this->getQrCodesObject() ){
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
        if( ! $qr_codes = $this->getQrCodesObject() ){
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