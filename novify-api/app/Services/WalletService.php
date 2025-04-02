<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Wallet;
use App\Models\Currency;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTransaction;
use App\Contracts\Payment\MobileMoneyContract;
use App\Contracts\Payment\CardPaymentContract;
use App\Contracts\Wallet\WalletBalanceContract;
use Illuminate\Support\Facades\Log;

class WalletService
{
    use ApiResponse;

    protected $mobileMoneyService;
    protected $cardPaymentService;
    protected $walletBalanceService;

    public function __construct(
        MobileMoneyContract $mobileMoneyService,
        CardPaymentContract $cardPaymentService,
        WalletBalanceContract $walletBalanceService
    ) {
        $this->mobileMoneyService = $mobileMoneyService;
        $this->cardPaymentService = $cardPaymentService;
        $this->walletBalanceService = $walletBalanceService;
    }

    /**
     * Create a default wallet for a merchant
     */
    public function createDefaultWallet(Merchant $merchant): Wallet
    {
        // Get the merchant's country's currency
        $currency = Currency::where('code', $merchant->country->currency_code)
            ->firstOrFail();

            $data = [
                'name' => 'Main Wallet',
                'type' => 'MAIN',
                'is_default' => true,
                'currency_code' => $currency->code
            ];

       return $this->createWallet($merchant, $data);
    }

    public function createWallet(Merchant $merchant, $data=null): Wallet
    {
        $currency = Currency::where('code', $data['currency_code'])->firstOrFail();
       
        return DB::transaction(function () use ($merchant, $currency, $data) {
            return  Wallet::create([
                'merchant_id' => $merchant->id,
                'name' => $data['name'] ?? 'Main Wallet',
                'wallet_number' => $this->generateWalletNumber($merchant),
                'currency_id' => $currency->id,
                'currency_code' => $currency->code,
                'type' => $data['type'] ?? 'MAIN',
                'is_active' => true,
                'is_default' => $data['is_default'] ?? false,
                'balance' =>  0
            ]);
        });
    }

    /**
     * Generate a unique wallet number
     */
    private function generateWalletNumber($merchant=null): string
    {
        $merchant = ($merchant)?$merchant:auth()->user();
        $number = str_pad($merchant->id . ($merchant->wallets()->count() + 1) . random_int(0, 999999), 12, '0', STR_PAD_LEFT);
        return $number;
    }

    //Get the merchant wallet balance
    public function getBalance(Wallet $wallet): array
    {   
        return $this->successResponse( $this->walletBalanceService->getBalance($wallet));
    }

    //Get the card details
    public function getCardDetails(Wallet $wallet): array
    {
        return $this->successResponse( $this->walletBalanceService->getCardDetails($wallet));
    }

    /**
     * Set wallet as default
     */
    public function setAsDefault(Wallet $wallet): array
    {
        return DB::transaction(function () use ($wallet) {
            // Remove default from other wallets
            $wallet->merchant->wallets()
                ->where('id', '!=', $wallet->id)
                ->update(['is_default' => false]);

            // Set this wallet as default
            $wallet->update(['is_default' => true]);

            return $this->successResponse([
                'wallet' => [
                    'id' => $wallet->id,
                    'name' => $wallet->name,
                    'wallet_number' => $wallet->wallet_number,
                    'balance' => $wallet->balance,
                    'currency_code' => $wallet->currency_code,
                    'type' => $wallet->type,
                    'is_default' => true
                ]
            ], 'Wallet set as default successfully');
        });
    }

    //Topup the wallet
    public function topup(Merchant $merchant, array $data): array
    {
        $wallet = $this->getTransactionWallet($merchant, $data);
        
        if (!$wallet) {
            return $this->errorResponse('Wallet not found', 404);
        }

        $transaction = $this->createWalletTransaction($data, null, $wallet, 'TOPUP');

        if ($data['payment_method'] === 'MOBILEMONEY') {
            $paymentStatus = $this->mobileMoneyService->receiveMoney([
                'amount' => $data['amount'],
                'phone' => $data['customer_number'] ?? null,
                'reference' => $transaction->tran_reference
            ]);
        }

        if ($data['payment_method'] === 'CARD') {
            $paymentStatus = $this->cardPaymentService->checkTransactionStatus([
                'amount' => $data['amount'],
                'reference' => $transaction->tran_reference
            ]);
        }

        if ($paymentStatus['success'] !== true) 
            return $this->errorResponse('Payment failed', 400);
        
        //Increase Balance
        $this->walletBalanceService->creditWallet($wallet, $data['amount']);
        //update transaction status
        $transaction->update(['tran_status' => 'SUCCESS']); 
        return $this->successResponse( $transaction, 'Wallet topup successful');
    }

    //Pay for an order
    public function pay(Merchant $merchant, array $data, $hasDestinationWallet = true,$isOrderCashPayment = false): array
    {
        Log::info('Payment request', ['data' => $data]);
        $wallet  =  ($isOrderCashPayment)?null:$this->getTransactionWallet($merchant, $data);
        $toWallet = ($hasDestinationWallet)?Wallet::where('wallet_number', $data['to_wallet_number'])->first():null;

        Log::info('Payment wallet', ['wallet' => $wallet]);
        Log::info('Payment to wallet', ['toWallet' => $toWallet]);
        if ($validationResult = $this->validateTransferEligibility($wallet, $data['amount'], $toWallet, $hasDestinationWallet,$isOrderCashPayment))
            return $validationResult;
       
        Log::info('Payment validation passed');

        $transaction = $this->createWalletTransaction($data, $wallet, $toWallet, 'PAYMENT');
        Log::info('Created Payment transaction', ['transaction' => $transaction]);
        $this->processWalletTransfer($wallet, $toWallet, $data['amount']);
        $transaction->update(['tran_status' => 'SUCCESS']);

        Log::info('Payment transaction', ['transaction' => $transaction]);
        return $this->successResponse( $transaction, 'Wallet payment successful');
    }

    //Transfer funds from one wallet to another
    public function transfer(Merchant $merchant, array $data): array
    {
        $wallet = $this->getTransactionWallet($merchant, $data);
        $toWallet = Wallet::where('wallet_number', $data['to_wallet_number'])->first();
        
        if ($validationResult = $this->validateTransferEligibility($wallet, $data['amount'], $toWallet))
            return $validationResult;
        
        $transaction = $this->createWalletTransaction($data, $wallet, $toWallet, 'TRANSFER');
        $this->processWalletTransfer($wallet, $toWallet, $data['amount']);
        $transaction->update(['tran_status' => 'SUCCESS']);

        return $this->successResponse( $transaction, 'Wallet transfer successful');
    }


    //Get the transaction details
    public function getTransaction(WalletTransaction $transaction): array
    {
        return $this->successResponse( $transaction->load(['fromWallet', 'toWallet', 'currency']), 'Transaction details retrieved successfully');
    }

    //Get the wallet for the transaction
    private function getTransactionWallet(Merchant $merchant, array $data): Wallet{
        return (isset($data['wallet_number']))?$merchant->wallets()->where('wallet_number', $data['wallet_number'])->first(): $merchant->wallets()->where('is_default', true)->first();
    }

    //Create a unique transaction reference
    private function createTransactionReference(): string
    {
        do {
            $reference = '01'.date('YmdHis') .auth()->user()->id.mt_rand(100, 999);
            $exists = WalletTransaction::where('tran_reference', $reference)->exists();
        } while ($exists);
        return $reference;
    }


    //Validate the transfer eligibility
    private function validateTransferEligibility(Wallet $fromWallet, float $amount, ?Wallet $toWallet = null, $hasDestinationWallet = true,$isOrderCashPayment = false): ?array 
    {
        if($isOrderCashPayment && $toWallet)
            return;

        if(($fromWallet && $toWallet) && $fromWallet->id === $toWallet->id) 
            return $this->errorResponse('Cannot transfer to the same wallet', 400);

        if (!$fromWallet) 
            return $this->errorResponse('Wallet not found', 404);

        if ($fromWallet->balance < $amount) 
            return $this->errorResponse('Insufficient balance', 400);
        
        if ($toWallet === null && $hasDestinationWallet) 
            return $this->errorResponse('Recipient wallet not found', 404);

       
        return null;
    }


    //Create a wallet transaction
    private function createWalletTransaction(array $data, Wallet $fromWallet=null, Wallet $toWallet=null, string $type = 'TRANSFER',int $impact=1 ): WalletTransaction
    {
        return DB::transaction(function () use ($data, $fromWallet, $toWallet, $type,$impact) {    
            return WalletTransaction::create([
                'tran_reference' => $this->createTransactionReference(),
                'from_wallet_id' => ($fromWallet)?$fromWallet->id:null,
                'to_wallet_id' => ($toWallet)?$toWallet->id:null,
                'currency_id' => ($fromWallet)?$fromWallet->currency_id:$toWallet->currency_id,
                'amount' => $data['amount'],
                'type' => $type,
                'tran_status' => 'PENDING',
                'payment_method' => $type === 'TRANSFER' ? 'WALLET' : $data['payment_method'],
                'payment_method_description' => $data['payment_method_description'] ?? null,
                'external_customer_number' => $data['customer_number'] ?? null,
                'narration' => $data['payment_description'] ?? "Wallet {$type} to {$toWallet?->wallet_number}",
                'tran_date' => now(),
                'net_impact' => $data['amount']*$impact,
                'from_merchant_id' => ($fromWallet)?$fromWallet->merchant_id:null,
                'to_merchant_id' => ($toWallet)?$toWallet->merchant_id:null
            ]);
        });
    }


    //Process the wallet transfer
    private function processWalletTransfer(Wallet $fromWallet, ?Wallet $toWallet, float $amount)
    {
        if($fromWallet) 
            $this->walletBalanceService->debitWallet($fromWallet, $amount);
        if($toWallet) 
            $this->walletBalanceService->creditWallet($toWallet, $amount);
    }

    
    /**
     * List merchant wallets
     */
    public function listWallets(Merchant $merchant, int $perPage = 20): array
    {
        $wallets = $merchant->wallets()
            ->orderBy('type')
            ->paginate($perPage);

        return $this->successResponse( $wallets);
    }

    //Get the wallet details
    public function getWallet(Wallet $wallet): array
    {
        return $this->successResponse($wallet->load(['merchant', 'currency', 'transactions']));
    }


    //Get the transactions
    public function getTransactions(array $filters): array
    {
        $transactions = WalletTransaction::with(['fromWallet', 'toWallet', 'currency'])
            ->when($filters['wallet_number'] ?? null, function ($query) use ($filters) {
                $query->whereHas('toWallet', function ($q) use ($filters) {
                    $q->where('wallet_number', $filters['wallet_number']);
                })->orWhereHas('fromWallet', function ($q) use ($filters) {
                    $q->where('wallet_number', $filters['wallet_number']);
                });
            })
            ->when($filters['type'] ?? null, function ($query) use ($filters) {
                $query->where('type', $filters['type']);
            })
            ->when($filters['status'] ?? null, function ($query) use ($filters) {
                $query->where('tran_status', $filters['status']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);

        return $this->successResponse( $transactions);
    }
} 