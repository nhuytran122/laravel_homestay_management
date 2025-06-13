<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
abstract class BaseUserInfoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email',
            'phone'     => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(?:\+84|0084|0)[235789][0-9]{8}$/',
            ],
            'address'   => 'nullable|string|max:255',
        ];
    }
}