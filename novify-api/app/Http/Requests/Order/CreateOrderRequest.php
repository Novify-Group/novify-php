<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Customer Information
            'customer' => 'required|array',
            'customer.name' => 'required|string|max:255',
            'customer.phone_number' => 'required|string|max:20',
            'customer.email' => 'nullable|email|max:255',
            'customer.address' => 'nullable|string|max:500',
            'customer.customer_merchant_id' => 'nullable|exists:merchants,id',

            // Order Details
            //'subtotal' => 'required|numeric|min:0',
           // 'tax_amount' => 'nullable|numeric|min:0',
            //'discount_amount' => 'nullable|numeric|min:0',
            //'total_amount' => 'required|numeric|min:0',
            //'notes' => 'nullable|string|max:1000',

            // Order Items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            //'items.*.tax_amount' => 'nullable|numeric|min:0',
            //'items.*.discount_amount' => 'nullable|numeric|min:0',
            //'items.*.total_amount' => 'required|numeric|min:0',

            // Payment Information (Optional)
            'payment' => 'nullable|array',
            'payment.bill_wallet_number' => 'required_with:payment|exists:wallets,wallet_number',
            'payment.amount' => 'required_with:payment|numeric|min:0.01',
            'payment.payment_method' => 'required_with:payment|in:WALLET,CARD,MOBILEMONEY,CASH,BANK,OTHER',
            'payment.payment_method_description' => 'nullable|string|max:255',
            'payment.customer_number' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'customer.required' => 'Customer information is required',
            'customer.name.required' => 'Customer name is required',
            'customer.phone_number.required' => 'Customer phone number is required',
            'items.required' => 'At least one order item is required',
            'items.*.product_id.exists' => 'One or more selected products do not exist',
            'items.*.variant_id.exists' => 'One or more selected product variants do not exist',
            'items.*.quantity.min' => 'Item quantity must be at least 1',
            'payment.bill_wallet_number.exists' => 'The paying wallet does not exist',
            'payment.amount.min' => 'The payment amount must be greater than zero',
            'payment.payment_method.in' => 'Invalid payment method selected'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Ensure numeric values are properly formatted
        if ($this->has('items')) {
            $this->merge([
                'items' => collect($this->items)->map(function ($item) {
                    return array_merge($item, [
                        'quantity' => (int) $item['quantity'],
                        'unit_price' => (float) $item['unit_price'],
                       // 'tax_amount' => (float) ($item['tax_amount'] ?? 0),
                       // 'discount_amount' => (float) ($item['discount_amount'] ?? 0),
                       // 'total_amount' => (float) $item['total_amount'],
                    ]);
                })->toArray()
            ]);
        }

        // Format payment amounts if present
        if ($this->has('payment') && isset($this->payment['amount'])) {
            $this->merge([
                'payment' => array_merge($this->payment, [
                    'amount' => (float) $this->payment['amount']
                ])
            ]);
        }
    }
} 