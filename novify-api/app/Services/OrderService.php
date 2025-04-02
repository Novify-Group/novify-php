<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Merchant;
use App\Traits\ApiResponse;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrderService
{
    use ApiResponse;

    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function create(Merchant $merchant, array $data): array
    {
        return DB::transaction(function () use ($merchant, $data) {
            // Create or update customer
            $customer = $this->createOrUpdateCustomer($merchant, $data['customer']);

            $data['total_amount'] = collect($data['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $data['subtotal'] = $data['total_amount'] - ($data['tax_amount'] ?? 0) - ($data['discount_amount'] ?? 0);
            // Create order
            $order = $this->createOrder($merchant, $customer, $data);
            // Create order items
            $this->createOrderItems($order, $data['items']);

            // Process payment if provided
            if (isset($data['payment']) && (isset($data['payment']['bill_wallet_number']) && $data['payment']['payment_method'] === 'WALLET')) {
               
                $order['wallet_number'] = $data['payment']['bill_wallet_number'];
                $transaction = $this->processPayment($merchant, $order, $data['payment']);
                Log::info('Payment transaction', ['transaction' => $transaction]);
                $order->update(['wallet_transaction_id' => $transaction->id]);
            }

            return $this->successResponse(
                ['order' => $order->load(['items.product', 'customer', 'walletTransaction'])],
                'Order created successfully',
                201
            );
        });
    }

    protected function createOrUpdateCustomer(Merchant $merchant, array $customerData): Customer
    {   
        if(!isset($customerData['phone_number']))
            return null;

        return Customer::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'phone_number' => $customerData['phone_number']
            ],
            [
                'name' => $customerData['name'],
                'email' => $customerData['email'] ?? null,
                'address' => $customerData['address'] ?? null,
                'customer_merchant_id' => $customerData['customer_merchant_id'] ?? null,
                'is_active' => true
            ]
        );
    }

    protected function createOrder(Merchant $merchant, Customer $customer=null, array $data): Order
    {
        return Order::create([
            'merchant_id' => $merchant->id,
            'customer_id' => ($customer) ? $customer->id : null,
            'order_number' => $this->generateOrderNumber(),
            'subtotal' => $data['subtotal'],
            'tax_amount' => $data['tax_amount'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'total_amount' => $data['total_amount'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null
        ]);
    }

    protected function createOrderItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
            $order->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_amount' => $item['tax_amount'] ?? 0,
                'discount_amount' => $item['discount_amount'] ?? 0,
                'total_amount' => $item['total_amount']
            ]);

            // Update product inventory
            $product->decrement('stock_quantity', $item['quantity']);
        }
    }

    protected function processPayment(Merchant $merchant, Order $order, array $paymentData): object
    {
        $paymentData['order_id'] = $order->id;
        $paymentData['order_description'] = "Payment for order #{$order->order_number}";
        $paymentData['amount'] = $order->total_amount;
        $paymentData['wallet_number'] = $order->wallet_number;

        Log::info('Payment data', ['paymentData' => $paymentData]);
        $response = $this->walletService->pay($merchant, $paymentData,false);
        Log::info('Payment processing response', ['response' => $response]);

        if (!isset($response['data'])) {
            Log::error('Payment processing failed', ['response' => $response]);
            throw new \Exception('Payment processing failed');
        }

        return $response['data'];
    }

    protected function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(Str::random(10));
    }

    public function list(Merchant $merchant, array $filters = [], int $perPage = 20): array
    {
        $orders = Order::where('merchant_id', $merchant->id)
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(isset($filters['customer_id']), function ($query) use ($filters) {
                $query->where('customer_id', $filters['customer_id']);
            })
            ->when(isset($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            })
            ->with(['items.product', 'customer', 'walletTransaction'])
            ->latest()
            ->paginate($perPage);

        return $this->successResponse(['orders' => $orders]);
    }

    public function show(Order $order): array
    {
        return $this->successResponse([
            'order' => $order->load(['items.product', 'customer', 'walletTransaction'])
        ]);
    }

    public function getCustomers(Merchant $merchant, array $filters = [], int $perPage = 20): array
    {
        $customers = Customer::where('merchant_id', $merchant->id)
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['search']}%")
                      ->orWhere('phone_number', 'like', "%{$filters['search']}%")
                      ->orWhere('email', 'like', "%{$filters['search']}%");
                });
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->withCount('orders')
            ->latest()
            ->paginate($perPage);

        return $this->successResponse(['customers' => $customers]);
    }
} 