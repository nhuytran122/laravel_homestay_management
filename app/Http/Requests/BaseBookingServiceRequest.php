<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseBookingServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function baseRules(): array
    {
        return [
            'services' => 'required|array',
            'services.*.serviceId' => 'required|exists:services,id',
            'services.*.description' => 'nullable|string|max:255',
        ];
    }
}