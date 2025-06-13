<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'unit'      => 'required|string|max:255',
            'price'     => 'required|numeric|min:0',
            'is_prepaid' => 'required|boolean',
        ];
    }
}