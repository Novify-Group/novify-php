<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class WalletCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:MAIN,INVESTMENT,SALES,SAVINGS,EXPENSES,OTHER',
            'currency_code' => 'required|string|max:3'
        ];
    }
}

