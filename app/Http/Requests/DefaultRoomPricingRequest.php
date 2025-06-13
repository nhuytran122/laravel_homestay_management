<?php

namespace App\Http\Requests;


class DefaultRoomPricingRequest extends BaseRoomPricingRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            
        ]);
    }
}