<?php 

namespace ReinaldoCoral\Pagseguro;


class Configure
{
    private $token;
    private $environment;

    public function __construct()
    {
        
    }

    public function setAccountCredentials( string $token )
    {
        $this->token = $token;
    }

    public function setEnvironment( $environment = 'sandbox' )
    {
        $this->environment = $environment;
    }

    public function getAccountToken()
    {
        return $this->token;
    }

    public function getEndpointBase()
    {
        return $this->environment === 'production' ? 'https://api.pagseguro.com' : 'https://sandbox.api.pagseguro.com';
    }

    public function getSecureEndpointBase()
    {
        return $this->environment === 'production' ? 'https://secure.api.pagseguro.com' : 'https://secure.sandbox.api.pagseguro.com';
    }

    public function getConnectEndpointBase()
    {
        return $this->environment === 'production' ? 'https://connect.pagbank.com.br' : 'https://connect.sandbox.pagbank.com.br';
    }

}