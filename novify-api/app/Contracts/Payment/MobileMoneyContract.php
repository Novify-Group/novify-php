<?php

namespace App\Contracts\Payment;

interface MobileMoneyContract
{
    public function sendMoney(array $data): array;
    public function receiveMoney(array $data): array;
    public function getBalance(array $data): array;
    public function getTransactions(array $data): array;
    public function getTransactionStatus(array $data): array;
    public function getTransactionDetails(array $data): array;
}   
