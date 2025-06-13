<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'address'   => 'required|string|max:255',
            'phone'     => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(?:\+84|0084|0)[235789][0-9]{8}$/',
            ],
            'gate_password' => [
                'required',
                'regex:/^\d{4,8}$/',
            ],
            'image' => 'nullable|file|mimes:jpg,png|max:2048',
            
        ];
    }
}