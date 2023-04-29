<?php

namespace App\Models;

class Customer
{
    protected $name;
    protected $surname;
    protected $email;
    protected $address;
    
    public function __construct($attr)
    {
        $this->name = encrypt($attr->name);
        $this->surname = encrypt($attr->surname);
        $this->email = encrypt($attr->email);
        $this->address = encrypt($attr->address);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSurname()
    {
        return $this->surname;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function getAddress()
    {
        return $this->address;
    }
    
}
