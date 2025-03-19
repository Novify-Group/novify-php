<?php

namespace App\Services\Payment;

use App\Contracts\Payment\CardPaymentContract;
class CardPaymentService
{
    protected $gateway;
    public function __construct( CardPaymentContract $gateway) {
        $this->gateway = $gateway;
    }

    public function checkTransactionStatus(array $data): array
    {
        return $this->gateway->checkTransactionStatus($data);
    }

    public function getTransactionDetails(array $data): array
    {
        return $this->gateway->getTransactionDetails($data);
    }
    
}