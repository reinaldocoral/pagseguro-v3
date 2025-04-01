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

    private $payload;

    public function __construct()
    {
        
    }

    public function setPayload( $payload )
    {
        $this->payload = $payload;
    }

    public function execute(Configure $config, $headers = [])
    {
        $search_id = $this->payload['search_id'] ?? null;

        if( strtoupper( substr($search_id, 0, 5) ) === 'CHEC_' ){
            return $this->searchCheckout($config, $search_id, $headers);
        }

        if( strtoupper( substr($search_id, 0, 5) ) === 'ORDE_' ){
            return $this->searchOrder($config, $search_id, $headers);
        }
        
        return $this->searchByReference($config, $search_id, $headers);
    }


    private function searchByReference(Configure $config, string $search_id, $headers = [])
    {
        $http_client = new Client(['base_uri' => $config->getEndpointBase()]);
        $response = $http_client->request('GET', '/charges', [
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
            ], $headers),
            'query'    => ['reference_id' => $search_id],
        ]);

        $busca = new HttpResponse($response);

        if( $busca->successful() ){
            if( $charge_id = $busca->object()[0]->id ?? null ){
                return $this->searchOrderByCobranca( $config, $charge_id, $headers );
            }
        }
        
        return null;
    }

    private function searchCheckout(Configure $config, string $search_id, $headers = [])
    {
        $http_client = new Client(['base_uri' => $config->getEndpointBase()]);
        $response = $http_client->request('GET', "/checkouts/$search_id", [
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
            ], $headers),
        ]);

        $busca = new HttpResponse($response);

        if( $busca->successful() ){
            if( $order = $busca->object()->orders[0] ?? null ){
                return $this->searchOrder( $config, $order->id, $headers );
            }
        }
        
        return null;
    }

    private function searchOrder(Configure $config, string $search_id, $headers = [])
    {
        $http_client = new Client(['base_uri' => $config->getEndpointBase()]);
        $response = $http_client->request('GET', "/orders/$search_id", [
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
            ], $headers),
        ]);

        $busca = new HttpResponse($response);

        if( $busca->successful() ){
            return $this->fillOrder( $busca->json() );
        }
        
        return null;
    }

    private function searchOrderByCobranca(Configure $config, string $charge_id, $headers = [])
    {
        $http_client = new Client(['base_uri' => $config->getEndpointBase()]);
        $response = $http_client->request('GET', '/orders', [
            'headers' => array_merge([
                'Authorization' => 'Bearer ' . $config->getAccountToken(),
                'Content-Type' => 'application/json',
            ], $headers),
            'query'    => ['charge_id' => $charge_id],
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
