<?php

namespace ReinaldoCoral\Pagseguro\Checkout;

use GuzzleHttp\Client;
use ReinaldoCoral\Pagseguro\Checkout\Objects\Address;
use ReinaldoCoral\Pagseguro\Checkout\Objects\Customer;
use ReinaldoCoral\Pagseguro\Checkout\Objects\Order;
use ReinaldoCoral\Pagseguro\Configure;
use ReinaldoCoral\Pagseguro\Domains\HttpResponse;

class PagamentoSearch
{
    private $config;

    public function __construct(Configure $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $search_id
     * @return Order
     */
    public function execute(string $search_id)
    {
        if( strtoupper( substr($search_id, 0, 5) ) === 'CHEC_' ){
            return $this->searchCheckout($search_id);
        }

        if( strtoupper( substr($search_id, 0, 5) ) === 'ORDE_' ){
            return $this->searchOrder($search_id);
        }
        
        return $this->searchByReference($search_id);
    }

    private function searchByReference(string $search_id)
    {
        $http_client = new Client(['base_uri' => $this->config->getEndpointBase()]);
        $response = $http_client->request('GET', "/charges?reference_id=$search_id" , [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getAccountToken(),
                'Content-Type' => 'application/json',
            ],
        ]);
        $busca = new HttpResponse($response);

        if( $busca->successful() ){
            if( $charge_id = $busca->object()[0]->id ?? null ){
                return $this->searchOrderByCobranca( $charge_id );
            }
        }
        
        return null;
    }

    private function searchCheckout(string $search_id)
    {
        $http_client = new Client(['base_uri' => $this->config->getEndpointBase()]);
        $response = $http_client->request('GET', "/checkouts/$search_id" , [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getAccountToken(),
                'Content-Type' => 'application/json',
            ],
        ]);
        $busca = new HttpResponse($response);

        if( $busca->successful() ){
            if( $order = $busca->object()->orders[0] ?? null ){
                return $this->searchOrder( $order->id );
            }
        }
        
        return null;
    }

    private function searchOrder(string $search_id)
    {
        $http_client = new Client(['base_uri' => $this->config->getEndpointBase()]);
        $response = $http_client->request('GET', "/orders/$search_id" , [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getAccountToken(),
                'Content-Type' => 'application/json',
            ],
        ]);
        $busca = new HttpResponse($response);

        if( $busca->successful() ){
            return $this->fillOrder( $busca->json() );
        }
        
        return null;
    }

    private function searchOrderByCobranca(string $charge_id)
    {
        $http_client = new Client(['base_uri' => $this->config->getEndpointBase()]);
        $response = $http_client->request('GET', "/orders?charge_id=$charge_id" , [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getAccountToken(),
                'Content-Type' => 'application/json',
            ],
        ]);
        $busca = new HttpResponse($response);

        if( $busca->successful() ){
            return $this->fillOrder( $busca->json()['orders'][0] ?? null );
        }
        
        return null;
    }

    private function fillOrder( $data )
    {
        $order = new Order();
        $order->setId( $data['id'] ?? null );
        $order->setReference( $data['reference_id'] ?? null );
        $order->setDate( $data['created_at'] ?? null );

        $customer = new Customer();
        $customer->setNome( $data['customer']['name'] ?? null );
        $customer->setEmail( $data['customer']['email'] ?? null );
        $customer->setCpfCnpj( $data['customer']['tax_id'] ?? null );
        $customer->setPhone( $data['customer']['phone'][0] ?? null );
        $order->setCustomer( $customer );

        $address = new Address();
        $address->setStreet( $data['shipping']['address']['street'] ?? null);
        $address->setNumber( $data['shipping']['address']['number'] ?? null);
        $address->setComplement( $data['shipping']['address']['complement'] ?? null);
        $address->setLocality( $data['shipping']['address']['locality'] ?? null);
        $address->setCity( $data['shipping']['address']['city'] ?? null);
        $address->setRegionCode( $data['shipping']['address']['region_code'] ?? null);
        $address->setCountry( $data['shipping']['address']['country'] ?? null);
        $address->setPostalCode( $data['shipping']['address']['postal_code'] ?? null);
        $order->setShipping($address);

        foreach ($data['items'] ?? [] as $item) {
            $order->addItems( $item );
        }

        foreach ($data['charges'] ?? [] as $charge) {
            $order->setCharges( $charge );
        }

        $order->setOrderStatus( $data['charges'][0]['status'] ?? null );
        return $order;
    }
}
