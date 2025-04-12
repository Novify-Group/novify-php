<?php

namespace App\Http\Controllers\API\Bills;

use App\Http\Controllers\API\BaseApiController;
use App\Models\BillerItem;
use App\Models\Wallet;
use App\Services\Bills\BillPaymentService;
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

        $validationResponse = $this->billPaymentService->validateBill($request->all());

        return $this->successResponse($validationResponse);
    }

    public function process(Request $request)
    {
        $request->validate([
            'biller_item_id' => 'required|exists:biller_items,id',
            'wallet_number' => 'required',
            'payment_method' => 'required',
            'bill_code' => 'required|string',
            'amount' => 'required|numeric|min:500',
            'validation_data' => 'nullable'
        ]);

       
        $payment = $this->billPaymentService->processBillPayment(
            $request->all()
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