<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Order\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * List all orders for the authenticated merchant
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
     * Create a new order
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
     * Get a specific order
     */
    public function show(Order $order): JsonResponse
    {
        return $this->execute(function () use ($order) {
            //$this->authorize('view', $order);
            return $this->orderService->show($order);
        });
    }

    /**
     * List all customers for the authenticated merchant
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