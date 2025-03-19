<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:branches,phone_number',
            'email' => 'nullable|email|unique:branches,email',
            'address' => 'required|string',
            'city' => 'required|string',
            'is_main_branch' => 'nullable|boolean'
        ];
    }
} 