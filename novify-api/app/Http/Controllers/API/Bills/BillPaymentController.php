<?php

namespace App\Http\Controllers\API\Bills;

use App\Http\Controllers\API\BaseApiController;
use App\Models\BillerItem;
use App\Models\Wallet;
use App\Services\Bills\BillPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class BillPaymentController extends BaseApiController
{
    protected $billPaymentService;

    public function __construct(BillPaymentService $billPaymentService)
    {
        $this->billPaymentService = $billPaymentService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/bills/validate",
     *     tags={"Bills"},
     *     summary="Validate bill payment",
     *     description="Validate a bill before processing payment",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"biller_item_id", "bill_code", "customer_number"},
     *             @OA\Property(property="biller_item_id", type="integer", example=1),
     *             @OA\Property(property="bill_code", type="string", example="1234567890"),
     *             @OA\Property(property="amount", type="number", format="float", example=50.00),
     *             @OA\Property(property="customer_number", type="string", example="1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill validated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bill validated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="customer_name", type="string", example="John Doe"),
     *                 @OA\Property(property="amount", type="number", example=50.00),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2024-12-31")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/bills/pay",
     *     tags={"Bills"},
     *     summary="Process bill payment",
     *     description="Process a bill payment using wallet or other payment methods",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"biller_item_id", "wallet_number", "payment_method", "bill_code", "amount"},
     *             @OA\Property(property="biller_item_id", type="integer", example=1),
     *             @OA\Property(property="wallet_number", type="string", example="123456789012"),
     *             @OA\Property(property="payment_method", type="string", enum={"WALLET", "MOBILEMONEY", "CARD"}, example="WALLET"),
     *             @OA\Property(property="bill_code", type="string", example="1234567890"),
     *             @OA\Property(property="amount", type="number", format="float", example=50.00),
     *             @OA\Property(property="validation_data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill payment processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bill payment processed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="reference", type="string", example="BILL202412011234567890"),
     *                 @OA\Property(property="status", type="string", example="SUCCESS"),
     *                 @OA\Property(property="amount", type="number", example=50.00)
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/bills/payments/{reference}",
     *     tags={"Bills"},
     *     summary="Get bill payment status",
     *     description="Get details and status of a specific bill payment",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="reference",
     *         in="path",
     *         required=true,
     *         description="Payment reference",
     *         @OA\Schema(type="string", example="BILL202412011234567890")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill payment retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="reference", type="string", example="BILL202412011234567890"),
     *                 @OA\Property(property="status", type="string", example="SUCCESS"),
     *                 @OA\Property(property="amount", type="number", example=50.00),
     *                 @OA\Property(property="biller_item", type="object"),
     *                 @OA\Property(property="wallet", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function show(string $reference)
    {
        $payment = BillPayment::where('reference', $reference)
            ->with(['billerItem.biller', 'wallet'])
            ->firstOrFail();

        return $this->successResponse($payment);
    }
} 