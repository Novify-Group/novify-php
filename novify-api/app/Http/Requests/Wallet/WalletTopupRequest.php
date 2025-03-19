<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class WalletTopupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wallet_number' => 'required|string|exists:wallets,wallet_number',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:WALLET,CARD,MOBILEMONEY,CASH,BANK,OTHER',
            'payment_method_description' => 'nullable|string|max:255',
            'customer_number' => 'nullable|required_if:payment_method,MOBILEMONEY|string',
            'otp' => 'nullable|string|max:8'
        ];
    }
} 