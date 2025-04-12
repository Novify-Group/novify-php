<?php

namespace App\Services\Bills;

use App\Models\BillPayment;
use App\Models\BillerItem;
use App\Models\Wallet;
use App\Services\Bills\BillerServiceConnection;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ValidationException;
use App\Exceptions\PaymentException;

class BillPaymentService
{
    protected $billerService;
    protected $walletService;

    public function __construct(
        BillerServiceConnection $billerService,
        WalletService $walletService
    ) {
        $this->billerService = $billerService;
        $this->walletService = $walletService;
    }

    public function validateBill(
        BillerItem $billerItem,
        string $billCode,
        ?float $amount = null
    ): array {
        try {
            $validationResponse = $this->billerService->validateBill(
                $billerItem->biller->code,
                $billCode,
                $amount
            );

            return $validationResponse;
        } catch (\Exception $e) {
            throw new ValidationException("Bill validation failed: " . $e->getMessage());
        }
    }

    public function processBillPayment(
        BillerItem $billerItem,
        Wallet $wallet,
        string $billCode,
        float $amount,
        int $paymentMethodId,
        array $validationData
    ): BillPayment {

        return DB::transaction(function () use (
            $billerItem,
            $wallet,
            $billCode,
            $amount,
            $paymentMethodId,
            $validationData
        ) {
            // Create payment record
            $payment = BillPayment::create([
                'biller_item_id' => $billerItem->id,
                'wallet_id' => $wallet->id,
                'payment_method_id' => $paymentMethodId,
                'bill_code' => $billCode,
                'amount' => $amount,
                'status' => 'pending',
                'reference' => $this->generateReference(),
                'validation_data' => $validationData
            ]);

            // Process payment with biller service
            try {
                $paymentResponse = $this->billerService->processBillPayment([
                    'reference' => $payment->reference,
                    'biller_code' => $billerItem->biller->code,
                    'bill_code' => $billCode,
                    'amount' => $amount,
                    'validation_data' => $validationData
                ]);

                // Deduct from wallet
                $this->walletService->debit(
                    $wallet,
                    $amount,
                    "Bill payment for {$billerItem->biller->name}",
                    'bill_payment',
                    $payment->id
                );

                // Update payment record
                $payment->update([
                    'status' => 'completed',
                    'provider_reference' => $paymentResponse['provider_reference'] ?? null,
                    'payment_data' => $paymentResponse
                ]);

                return $payment;
                
            } catch (\Exception $e) {
                $payment->update([
                    'status' => 'failed',
                    'meta_data' => ['error' => $e->getMessage()]
                ]);
                throw new PaymentException("Payment failed: " . $e->getMessage());
            }
        });
    }

    protected function generateReference(): string
    {
        return 'BILL-' . uniqid() . '-' . time();
    }
} 