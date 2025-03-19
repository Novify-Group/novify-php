<?php

namespace App\Services\Payment\Integrations;

use App\Contracts\Payment\CardPaymentContract;

class CardPaymentGateway implements CardPaymentContract
{
    public function checkTransactionStatus(array $data): array
    {
        sleep(1);
        return ["success" => true];
    }

    public function getTransactionDetails(array $data): array   
    {
        return [];
    }   
    
}