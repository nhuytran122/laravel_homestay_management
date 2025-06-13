<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomTypeRequest extends DefaultRoomPricingRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = parent::rules();

        return array_merge($rules, [
            'name' => 'required|string|max:255',
            'max_guest' => 'required|numeric|min:1',
        ]);
    }
}