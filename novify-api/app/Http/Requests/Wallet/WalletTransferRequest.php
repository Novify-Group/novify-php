<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class WalletTransferRequest extends FormRequest
{
  
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_wallet_number' => 'required|exists:wallets,wallet_number',
            'wallet_number' => 'nullable|exists:wallets,wallet_number',
            'amount' => 'required|numeric|min:500',
            'payment_description' => 'nullable|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'to_wallet_id.exists' => 'The recipient wallet does not exist.',
            'amount.min' => 'The transfer amount must atleast 500.'
        ];
    }
} 