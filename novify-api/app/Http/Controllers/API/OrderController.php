<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Order\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class OrderController extends BaseApiController
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/merchant/orders",
     *     tags={"Orders"},
     *     summary="List orders",
     *     description="Get a list of orders for the authenticated merchant",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of orders per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by order status",
     *         @OA\Schema(type="string", enum={"pending", "completed", "cancelled"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="orders", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->orderService->list(
                ($request->user()->merchant) ? $request->user()->merchant : $request->user(),
                $request->all(),
                $request->input('per_page', 20)
            );
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/merchant/orders",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     description="Create a new order with customer and items",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer", "items"},
     *             @OA\Property(property="customer", type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="phone_number", type="string", example="1234567890"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="address", type="string", example="123 Main St")
     *             ),
     *             @OA\Property(property="items", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=2),
     *                 @OA\Property(property="unit_price", type="number", format="float", example=25.00)
     *             )),
     *             @OA\Property(property="payment", type="object",
     *                 @OA\Property(property="payment_method", type="string", enum={"CASH", "WALLET", "MOBILEMONEY", "CARD"}, example="CASH"),
     *                 @OA\Property(property="bill_wallet_number", type="string", example="123456789012")
     *             ),
     *             @OA\Property(property="notes", type="string", example="Special delivery instructions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="order", type="object"),
     *                 @OA\Property(property="transaction", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->orderService->create(
                ($request->user()->merchant) ? $request->user()->merchant : $request->user(),
                $request->validated()
            );
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/merchant/orders/{order}",
     *     tags={"Orders"},
     *     summary="Get specific order",
     *     description="Get details of a specific order",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="order", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function show(Order $order): JsonResponse
    {
        return $this->execute(function () use ($order) {
            //$this->authorize('view', $order);
            return $this->orderService->show($order);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/merchant/orders/customers",
     *     tags={"Orders"},
     *     summary="Get order customers",
     *     description="Get list of customers who have placed orders",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of customers per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by customer name or phone",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="customers", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function customers(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->orderService->getCustomers(
                $request->user()->merchant,
                $request->all(),
                $request->input('per_page', 20)
            );
        });
    }
} 