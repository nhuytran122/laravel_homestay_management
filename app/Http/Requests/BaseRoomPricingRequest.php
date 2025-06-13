<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRoomPricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'base_duration' => 'required|numeric|min:0',
            'base_price'    => 'required|numeric|min:0',
            'extra_hour_price' => 'required|numeric|min:0',
            'overnight_price'   => 'required|numeric|min:0',
            'daily_price'   => 'required|numeric|min:0'
        ];
    }
}