<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|unique:branches,phone_number,' . $this->branch->id,
            'email' => 'nullable|email|unique:branches,email,' . $this->branch->id,
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'is_main_branch' => 'nullable|boolean'
        ];
    }
} 