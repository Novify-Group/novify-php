<?php

namespace App\Contracts\Payment;

interface CardPaymentContract
{
    public function checkTransactionStatus(array $data): array;
    public function getTransactionDetails(array $data): array;
}