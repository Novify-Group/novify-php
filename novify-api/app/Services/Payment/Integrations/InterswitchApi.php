<?php

namespace App\Services\Payment\Integrations;

use App\Contracts\Payment\MobileMoneyContract;

class InterswitchApi implements MobileMoneyContract
{
    public function sendMoney(array $data): array
    {
        return [];
    }

    public function receiveMoney(array $data): array        
    {
        sleep(1);
        return ["success" => true];
    }

    public function getBalance(array $data): array
    {
        return [];
    }

    public function getTransactions(array $data): array
    {
        return [];
    }

    public function getTransactionStatus(array $data): array
    {
        return [];
    }

    public function getTransactionDetails(array $data): array
    {
        return [];
    }
    
}