<?php

namespace ReinaldoCoral\Pagseguro\Checkout\Objects;

class Address {
    private string $street;
    private string $number;
    private string $complement;
    private string $locality;
    private string $city;
    private string $region;
    private string $region_code;
    private string $country;
    private string $postal_code;

    public function setStreet($value)
    {
        $this->street = $value;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function setNumber($value)
    {
        $this->number = $value;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setComplement($value)
    {
        $this->complement = $value;
    }

    public function getComplement()
    {
        return $this->complement;
    }

    public function setLocality($value)
    {
        $this->locality = $value;
    }

    public function getLocality()
    {
        return $this->locality;
    }

    public function setCity($value)
    {
        $this->city = $value;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setRegion($value)
    {
        $this->region = $value;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegionCode($value)
    {
        $this->region_code = $value;
    }

    public function getRegionCode()
    {
        return $this->region_code;
    }

    public function setCountry($value)
    {
        $this->country = $value;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setPostalCode($value)
    {
        $this->postal_code = $value;
    }

    public function getPostalCode()
    {
        return $this->postal_code;
    }

}