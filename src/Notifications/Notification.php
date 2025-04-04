<?php

namespace ReinaldoCoral\Pagseguro\Notifications;

use ReinaldoCoral\Pagseguro\Checkout\Objects\Address;
use ReinaldoCoral\Pagseguro\Checkout\Objects\Customer;
use ReinaldoCoral\Pagseguro\Checkout\Objects\Order;
use ReinaldoCoral\Pagseguro\Configure;

class Notification
{
    private $payload;
    private $data;
    private $config;
    private $headers;

    public function __construct(Configure $config)
    {
        $this->payload = file_get_contents('php://input');
        $this->headers = getallheaders();
        $this->data = json_decode($this->payload, true);
        $this->config = $config;
    }
    
    public function hasValidNotification()
    {
        return ( $this->data !== null ) && $this->hasAuthenticity();
    }

    private function hasAuthenticity()
    {
        $signature = hash('sha256', $this->config->getAccountToken() . '-' . $this->payload);
        $authenticity_token = $this->headers['X-Authenticity-Token'] ?? null;
        return $signature === $authenticity_token;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function read()
    {        
        if ( $this->hasValidNotification() ) { 
            $data = $this->data;
            $transaction = new Order();
            $transaction->setId( $data['id'] ?? null );
            $transaction->setReference( $data['reference_id'] ?? null );
            $transaction->setDate( $data['created_at'] ?? null );

            $customer = new Customer();
            $customer->setNome( $data['customer']['name'] ?? null );
            $customer->setEmail( $data['customer']['email'] ?? null );
            $customer->setCpfCnpj( $data['customer']['tax_id'] ?? null );
            $customer->setPhone( $data['customer']['phone'][0] ?? null );
            $transaction->setCustomer( $customer );

            $address = new Address();
            $address->setStreet( $data['shipping']['address']['street'] ?? null);
            $address->setNumber( $data['shipping']['address']['number'] ?? null);
            $address->setLocality( $data['shipping']['address']['locality'] ?? null);
            $address->setCity( $data['shipping']['address']['city'] ?? null);
            $address->setRegionCode( $data['shipping']['address']['region_code'] ?? null);
            $address->setCountry( $data['shipping']['address']['country'] ?? null);
            $address->setPostalCode( $data['shipping']['address']['postal_code'] ?? null);
            if( $data['shipping']['address']['complement'] ?? null ){
                $address->setComplement( $data['shipping']['address']['complement'] ?? null);
            }
            $transaction->setShipping($address);

            foreach ($data['items'] ?? [] as $item) {
                $transaction->addItems( $item );
            }

            foreach ($data['charges'] ?? [] as $charge) {
                $transaction->setCharges( $charge );
            }

            $transaction->setOrderStatus( $data['charges'][0]['status'] ?? null );
            return $transaction;
        }

        return null;
    }

}
