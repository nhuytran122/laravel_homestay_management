<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('customer_type');

        return [
            'name' => 'required|string|max:255|unique:customer_types,name' . ($id ? ',' . $id : ''),
            'discount_rate' => 'required|numeric|min:0|max:100',
            'min_point' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}