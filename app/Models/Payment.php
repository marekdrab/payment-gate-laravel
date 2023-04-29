<?php

namespace App\Models;

class Payment
{
    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_GBP = 'GBP';

    const STATUS_PENDING = 'PENDING';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_FAILED = 'FAILED';
    const STATUS_EXPIRED = 'EXPIRED';


    protected $id;
    protected $payer;
    protected $amount;
    protected $currency;
    protected $provider;
    protected $status;
    protected $expires_at;

    public function __construct($attr)
    {
        $this->id = rand(1000,9999); //Just random number - connected with database would be something else and much safer
        $this->payer = $attr->payer;
        $this->amount = $attr->amount;
        $this->currency = $attr->currency;
        $this->provider = $attr->provider;
        $this->status = Payment::STATUS_PENDING;
        $this->expires_at = time() + 24 * 60 * 60;
    }

    public function getId() 
    {
        return $this->id;
    }
    
    public function getPayer()
    {
        return $this->payer;
    }
    
    public function getAmount()
    {
        return $this->amount;
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }
    
    public function getProvider()
    {
        return $this->provider;
    }

    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }

    public function getExpiresAt() {
        return $this->expires_at;
    }
}