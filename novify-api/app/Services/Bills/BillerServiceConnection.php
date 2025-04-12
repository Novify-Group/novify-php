<?php

namespace App\Services\Bills;

interface BillerServiceConnection
{
    public function validateBill($data): array;
    public function processBillPayment($data): array;

    public function checkTransactionStatus($data): array;

    public function getBillerItems($data): array;

    public function getBillerItem($data): array;

    public function getBillerItemByCode($data): array;

    public function getBillerItemById($data): array;

} 