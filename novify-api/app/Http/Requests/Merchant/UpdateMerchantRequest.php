<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMerchantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => 'sometimes|exists:countries,id',
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'dob' => 'sometimes|date|before:today',
            'id_type' => 'sometimes|string',
            'id_number' => 'sometimes|string',
            'id_picture' => 'sometimes|string', // base64
            'passport_photo' => 'sometimes|string', // base64
            'phone_number' => 'sometimes|string|unique:merchants,phone_number,' . $this->merchant->id,
            'email' => 'sometimes|email|unique:merchants,email,' . $this->merchant->id,
            'store_name' => 'sometimes|string|min:6',
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