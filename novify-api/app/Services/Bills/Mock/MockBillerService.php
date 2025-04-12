<?php

namespace App\Services\Bills\Mock;

use App\Services\Bills\BillerServiceConnection;
use App\Exceptions\ValidationException;
use App\Exceptions\PaymentException;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;

class MockBillerService implements BillerServiceConnection
{
    use ApiResponse;

    public function validateBill($data): array
    {
        try {
            Log::info('Bill validation request', ['data' => $data]);
            
            // Simulate API delay
            usleep(500000); // 0.5 seconds

            // Mock validation logic based on biller code
            $validationResponse = match ($data['biller_code']) {
                'MYMARKET' => $this->validateMyMarketBill($data),
                'CITYUTIL' => $this->validateUtilityBill($data),
                'FASTNET' => $this->validateInternetBill($data),
                default => throw new ValidationException("Invalid biller code: {$data['biller_code']}")
            };

            Log::info('Bill validation response', ['response' => $validationResponse]);
            return $this->successResponse($validationResponse, 'Bill validation successful');

        } catch (ValidationException $e) {
            Log::error('Bill validation error', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Bill validation error', ['error' => $e->getMessage()]);
            return $this->errorResponse('An error occurred during bill validation', 500);
        }
    }

    public function processBillPayment($data): array
    {
        try {
            Log::info('Bill payment request', ['data' => $data]);
            
            // Simulate API delay
            usleep(1000000); // 1 second

            // Simulate payment processing
            if (rand(1, 100) <= 95) { // 95% success rate
                $paymentResponse = [
                    'status' => 'success',
                    'message' => 'Payment processed successfully',
                    'provider_reference' => 'PRV-' . strtoupper(uniqid()),
                    'transaction_date' => now()->toIso8601String(),
                    'biller_code' => $data['biller_code'],
                    'bill_code' => $data['bill_code'],
                    'amount' => $data['amount'],
                    'receipt_number' => 'RCP-' . strtoupper(uniqid()),
                    'meta_data' => [
                        'provider_name' => 'Mock Biller Service',
                        'processing_time' => '1.0s'
                    ]
                ];

                Log::info('Bill payment response', ['response' => $paymentResponse]);
                return $this->successResponse($paymentResponse, 'Payment processed successfully');
            }

            throw new PaymentException('Payment processing failed. Please try again.');

        } catch (PaymentException $e) {
            Log::error('Bill payment error', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Bill payment error', ['error' => $e->getMessage()]);
            return $this->errorResponse('An error occurred during payment processing', 500);
        }
    }

    public function checkTransactionStatus($data): array
    {
        try {
            Log::info('Transaction status check request', ['data' => $data]);
            
            $statusResponse = [
                'status' => 'completed',
                'transaction_reference' => $data['reference'] ?? null,
                'provider_reference' => 'PRV-' . strtoupper(uniqid()),
                'transaction_date' => now()->toIso8601String(),
                'amount' => $data['amount'] ?? 0,
            ];

            return $this->successResponse($statusResponse, 'Transaction status retrieved successfully');
        } 
        catch (\Exception $e) {
            Log::error('Transaction status check error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to check transaction status', 500);
        }
    }

    public function getBillerItems($data): array
    {
        try {
            $items = [
                [
                    'id' => 1,
                    'name' => 'Rent Payment',
                    'code' => 'RENT-PAY',
                    'description' => 'Monthly rent payment',
                    'min_amount' => 100.00,
                    'max_amount' => 100000.00,
                    'is_amount_fixed' => false
                ],
                // Add more mock items as needed
            ];

            return $this->successResponse($items, 'Biller items retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Get biller items error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to retrieve biller items', 500);
        }
    }

    public function getBillerItem($data): array
    {
        try {
            $item = [
                'id' => 1,
                'name' => 'Rent Payment',
                'code' => 'RENT-PAY',
                'description' => 'Monthly rent payment',
                'min_amount' => 100.00,
                'max_amount' => 100000.00,
                'is_amount_fixed' => false
            ];

            return $this->successResponse($item, 'Biller item retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Get biller item error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to retrieve biller item', 500);
        }
    }

    private function validateMyMarketBill($data): array
    {
        if (!preg_match('/^RENT-\d{6}$/', $data['bill_code'])) {
            throw new ValidationException('Invalid rent payment code format. Expected: RENT-XXXXXX');
        }

        return [
            'valid' => true,
            'bill_details' => [
                'tenant_name' => 'John Doe',
                'property_name' => 'Market Unit ' . substr($data['bill_code'], -3),
                'rent_period' => now()->format('F Y'),
                'due_amount' => $data['amount'] ?? 1500.00,
                'due_date' => now()->addDays(5)->format('Y-m-d'),
                'late_fee' => 50.00
            ]
        ];
    }

    private function validateUtilityBill($data): array
    {
        if (!preg_match('/^(WATER|ELEC)-\d{8}$/', $data['bill_code'])) {
            throw new ValidationException('Invalid utility bill format. Expected: (WATER|ELEC)-XXXXXXXX');
        }

        $type = explode('-', $data['bill_code'])[0];
        $mockAmount = $type === 'WATER' ? 75.50 : 150.25;

        return [
            'valid' => true,
            'bill_details' => [
                'customer_name' => 'Jane Smith',
                'account_number' => substr($data['bill_code'], -8),
                'bill_period' => now()->format('F Y'),
                'due_amount' => $data['amount'] ?? $mockAmount,
                'due_date' => now()->addDays(14)->format('Y-m-d'),
                'meter_reading' => rand(1000, 9999),
                'utility_type' => $type
            ]
        ];
    }

    private function validateInternetBill($data): array
    {
        if (!preg_match('/^NET-\d{10}$/', $data['bill_code'])) {
            throw new ValidationException('Invalid internet bill format. Expected: NET-XXXXXXXXXX');
        }

        return [
            'valid' => true,
            'bill_details' => [
                'customer_name' => 'Bob Johnson',
                'account_number' => substr($data['bill_code'], -10),
                'plan_name' => 'High Speed Fiber',
                'billing_period' => now()->format('F Y'),
                'due_amount' => 49.99, // Fixed amount for internet plan
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'service_status' => 'Active'
            ]
        ];
    }

    public function getBillerItemByCode($data): array
    {
        try {
            $item = [
                'id' => 1,
                'name' => 'Rent Payment',
                'code' => $data['code'] ?? 'RENT-PAY',
                'description' => 'Monthly rent payment',
                'min_amount' => 100.00,
                'max_amount' => 100000.00,
                'is_amount_fixed' => false
            ];

            return $this->successResponse($item, 'Biller item retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Get biller item by code error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to retrieve biller item', 500);
        }
    }

    public function getBillerItemById($data): array
    {
        try {
            $item = [
                'id' => $data['id'] ?? 1,
                'name' => 'Rent Payment',
                'code' => 'RENT-PAY',
                'description' => 'Monthly rent payment',
                'min_amount' => 100.00,
                'max_amount' => 100000.00,
                'is_amount_fixed' => false
            ];

            return $this->successResponse($item, 'Biller item retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Get biller item by ID error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to retrieve biller item', 500);
        }
    }
}
