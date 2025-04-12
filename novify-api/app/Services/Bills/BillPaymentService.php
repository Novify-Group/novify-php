<?php

namespace App\Services\Bills;

use App\Models\BillPayment;
use App\Models\BillerItem;
use App\Models\Wallet;
use App\Services\Bills\BillerServiceConnection;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ValidationException;
use App\Exceptions\PaymentException;
use App\Services\WalletService;


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
        array $data
    ): array {
        try {
            $validationResponse = $this->billerService->validateBill(
                $data
            );

            return $validationResponse;
        } catch (\Exception $e) {
            throw new ValidationException("Bill validation failed: " . $e->getMessage());
        }
    }

    public function processBillPayment(
        array $data
    ): BillPayment {

        return DB::transaction(function () use (
            $data
        ) {
            // Create payment record
            $payment = BillPayment::create([
                'biller_item_id' => $data['biller_item_id'],
                'wallet_id' => $data['wallet_id'],
                'payment_method_id' => $data['payment_method_id'],
                'bill_code' => $data['bill_code'],
                'amount' => $data['amount'],
                'status' => 'pending',
                'reference' => $this->generateReference(),
                'validation_data' => $data['validation_data']
            ]);

            // Process payment with biller service
            try {
                $paymentResponse = $this->billerService->processBillPayment([
                    'reference' => $payment->reference,
                    'biller_code' => $data['biller_code'],
                    'bill_code' => $data['bill_code'],
                    'amount' => $data['amount'],
                    'validation_data' => $data['validation_data']
                ]);

                // // Deduct from wallet
                // $this->walletService->debit(
                //     $wallet,
                //     $amount,
                //     "Bill payment for {$billerItem->biller->name}",
                //     'bill_payment',
                //     $payment->id
                // );

                // Update payment record
                $payment->update([
                    'status' => 'COMPLETED',
                    'provider_reference' => $paymentResponse['provider_reference'] ?? null,
                    'payment_data' => $paymentResponse
                ]);

                return $payment;
                
            } catch (\Exception $e) {
                $payment->update([
                    'status' => 'FAILED',
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