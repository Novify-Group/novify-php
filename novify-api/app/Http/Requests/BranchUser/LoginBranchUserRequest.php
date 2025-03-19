<?php

namespace App\Http\Requests\BranchUser;

use Illuminate\Foundation\Http\FormRequest;

class LoginBranchUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string', // For email or phone
            'password' => 'required|string'
        ];
    }
} 