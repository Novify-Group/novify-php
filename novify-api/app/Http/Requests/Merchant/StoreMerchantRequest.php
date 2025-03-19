<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class StoreMerchantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => 'required|exists:countries,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date|before:today',
            'id_type' => 'required|string',
            'id_number' => 'required|string',
            'id_picture' => 'required|string', // base64
            'passport_photo' => 'required|string', // base64
            'phone_number' => 'required|string|unique:merchants',
            'email' => 'required|email|unique:merchants',
            'password' => 'required|min:8',
            'store_name' => 'required|string|min:6',
            'store_logo' => 'nullable|string', // base64
            'store_description' => 'nullable|string|max:1000',
            'licence_number' => 'nullable|string|max:255',
            'tax_id_number' => 'nullable|string|max:255',
            'is_licenced' => 'nullable|boolean',
            'date_started' => 'nullable|date',
            'market_area_id' => 'nullable|exists:market_areas,id',
        ];
    }
} 