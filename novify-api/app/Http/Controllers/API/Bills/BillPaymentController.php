<?php

namespace App\Http\Controllers\API\Bills;

use App\Http\Controllers\API\BaseApiController;
use App\Models\BillerItem;
use App\Models\Wallet;
use App\Services\BillPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillPaymentController extends BaseApiController
{
    protected $billPaymentService;

    public function __construct(BillPaymentService $billPaymentService)
    {
        $this->billPaymentService = $billPaymentService;
    }

    public function validate(Request $request)
    {
        $request->validate([
            'biller_item_id' => 'required|exists:biller_items,id',
            'bill_code' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
            'customer_number' => 'required|string'
        ]);

        $billerItem = BillerItem::findOrFail($request->biller_item_id);
        
        $validationResponse = $this->billPaymentService->validateBill(
            $billerItem,
            $request->bill_code,
            $request->amount
        );

        return $this->successResponse($validationResponse);
    }

    public function process(Request $request)
    {
        $request->validate([
            'biller_item_id' => 'required|exists:biller_items,id',
            'wallet_id' => 'required|exists:wallets,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bill_code' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'validation_data' => 'required'
        ]);

        $billerItem = BillerItem::findOrFail($request->biller_item_id);
        $wallet = Wallet::findOrFail($request->wallet_id);

        $payment = $this->billPaymentService->processBillPayment(
            $billerItem,
            $wallet,
            $request->bill_code,
            $request->amount,
            $request->payment_method_id,
            $request->validation_data
        );

        return $this->successResponse($payment);
    }

    public function show(string $reference)
    {
        $payment = BillPayment::where('reference', $reference)
            ->with(['billerItem.biller', 'wallet'])
            ->firstOrFail();

        return $this->successResponse($payment);
    }
} 