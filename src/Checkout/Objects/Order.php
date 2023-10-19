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
}