<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Wallet\WalletTopupRequest;
use App\Http\Requests\Wallet\WalletTransferRequest;
use App\Services\WalletService;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Requests\Wallet\WalletPayRequest;
use App\Http\Requests\Wallet\WalletCreateRequest;
use OpenApi\Annotations as OA;

class WalletController extends BaseApiController
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/wallet/topup",
     *     tags={"Wallets"},
     *     summary="Top up wallet",
     *     description="Add funds to a wallet using various payment methods",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "payment_method"},
     *             @OA\Property(property="amount", type="number", format="float", example=100.00),
     *             @OA\Property(property="payment_method", type="string", enum={"MOBILEMONEY", "CARD", "CASH"}, example="MOBILEMONEY"),
     *             @OA\Property(property="customer_number", type="string", example="1234567890"),
     *             @OA\Property(property="wallet_number", type="string", example="123456789012")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallet topped up successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your topup was successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="tran_reference", type="string", example="01202412011234567890123"),
     *                 @OA\Property(property="amount", type="number", example=100.00),
     *                 @OA\Property(property="tran_status", type="string", example="SUCCESS")
     *             )
     *         )
     *     )
     * )
     */
    public function topup(WalletTopupRequest $request)
    { 
        return $this->execute(function () use ($request) {
            return $this->walletService->topup($request->user(), $request->validated());
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/wallet/transfer",
     *     tags={"Wallets"},
     *     summary="Transfer money between wallets",
     *     description="Transfer money from one wallet to another",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_wallet_number", "to_wallet_number", "amount"},
     *             @OA\Property(property="from_wallet_number", type="string", example="123456789012"),
     *             @OA\Property(property="to_wallet_number", type="string", example="987654321098"),
     *             @OA\Property(property="amount", type="number", format="float", example=50.00),
     *             @OA\Property(property="narration", type="string", example="Transfer to savings")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transfer successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Transfer successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="tran_reference", type="string", example="01202412011234567890123"),
     *                 @OA\Property(property="amount", type="number", example=50.00),
     *                 @OA\Property(property="tran_status", type="string", example="SUCCESS")
     *             )
     *         )
     *     )
     * )
     */
    public function transfer(WalletTransferRequest $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->transfer($request->user(), $request->validated());
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/wallet/pay",
     *     tags={"Wallets"},
     *     summary="Pay for order or service",
     *     description="Pay for an order or service using wallet funds",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_wallet_number", "to_wallet_number", "amount"},
     *             @OA\Property(property="from_wallet_number", type="string", example="123456789012"),
     *             @OA\Property(property="to_wallet_number", type="string", example="987654321098"),
     *             @OA\Property(property="amount", type="number", format="float", example=25.00),
     *             @OA\Property(property="payment_method", type="string", enum={"WALLET", "CASH", "MOBILEMONEY", "CARD"}, example="WALLET"),
     *             @OA\Property(property="order_id", type="string", example="ORD-000001"),
     *             @OA\Property(property="narration", type="string", example="Payment for order ORD-000001")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="tran_reference", type="string", example="01202412011234567890123"),
     *                 @OA\Property(property="amount", type="number", example=25.00),
     *                 @OA\Property(property="tran_status", type="string", example="SUCCESS")
     *             )
     *         )
     *     )
     * )
     */
    public function pay(WalletPayRequest $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->pay($request->user(), $request->validated());
        });
    }

    public function balance(Wallet $wallet)
    {
        return $this->execute(function () use ($wallet) {
            return $this->walletService->getBalance($wallet);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/wallet/transactions",
     *     tags={"Wallets"},
     *     summary="Get wallet transactions",
     *     description="Get transaction history for wallets",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="wallet_number",
     *         in="query",
     *         description="Filter by wallet number",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by transaction type",
     *         @OA\Schema(type="string", enum={"TOPUP", "CASHOUT", "DEBIT", "FEES", "SALE", "TRANSFER", "PAYMENT"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of transactions per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transactions retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="transactions", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getTransactions(Request $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->getTransactions($request->all());
        });
    }

    
    /**
     * @OA\Get(
     *     path="/api/v1/wallet/transactions/{transaction}",
     *     tags={"Wallets"},
     *     summary="Get specific transaction",
     *     description="Get details of a specific wallet transaction",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="transaction",
     *         in="path",
     *         required=true,
     *         description="Transaction ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="transaction", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getTransaction(WalletTransaction $transaction)
    { 
        return $this->execute(function () use ($transaction) {
            return $this->walletService->getTransaction($transaction);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/wallet/",
     *     tags={"Wallets"},
     *     summary="Create new wallet",
     *     description="Create a new wallet for the merchant",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "currency_id"},
     *             @OA\Property(property="name", type="string", example="Savings Wallet"),
     *             @OA\Property(property="currency_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", enum={"MAIN", "SAVINGS", "EXPENSES", "INVESTMENT", "OTHER"}, example="SAVINGS"),
     *             @OA\Property(property="is_default", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Wallet created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Wallet created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="wallet", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function create(WalletCreateRequest $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->createWallet($request->user(), $request->validated());
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/wallet/",
     *     tags={"Wallets"},
     *     summary="Get merchant wallets",
     *     description="Get all wallets for the authenticated merchant",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of wallets per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallets retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="wallets", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getWallets(Request $request)
    {
        return $this->execute(function () use ($request) {
            return $this->walletService->listWallets($request->user(), $request->per_page ?? 20);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/wallet/{wallet}",
     *     tags={"Wallets"},
     *     summary="Get specific wallet",
     *     description="Get details of a specific wallet",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="wallet",
     *         in="path",
     *         required=true,
     *         description="Wallet ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wallet retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="wallet", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getWallet(Wallet $wallet)       
    {
        return $this->execute(function () use ($wallet) {
            return $this->walletService->getWallet($wallet);
        });
    }
    

} 