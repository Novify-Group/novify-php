<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class WalletPayRequest extends FormRequest
{
  
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_wallet_number' => 'required|exists:wallets,wallet_number',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:WALLET,CARD,MOBILEMONEY,CASH,BANK,OTHER',
            'payment_method_description' => 'nullable|string|max:255',
            'customer_number' => 'nullable|string|max:255',
            "order_id" => "nullable|numeric|min:1",
            "order_description" => "nullable|string|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            'to_wallet_id.exists' => 'The recipient wallet does not exist.',
            'amount.min' => 'The transfer amount must be greater than zero.',
            'payment_method.in' => 'Invalid payment method selected.'
        ];
    }
} 