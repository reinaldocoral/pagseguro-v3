<?php

namespace ReinaldoCoral\Pagseguro\Checkout\Objects;

class Customer {
    private string $nome;
    private string $email;
    private string $tax_id;
    private $phone;

    public function setNome($value)
    {
        $this->nome = $value;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setCpfCnpj($value)
    {
        $this->tax_id = $value;
    }

    public function getCpfCnpj()
    {
        return $this->tax_id;
    }

    public function setPhone($value)
    {
        $this->phone[] = $value;
    }

    public function getPhone()
    {
        return $this->phone;
    }

}