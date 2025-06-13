<?php

namespace App\Http\Requests;

use App\Services\RoleService;
use Illuminate\Validation\Rule;

class CustomerRequest extends BaseUserInfoRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = parent::rules();
        $id = $this->route('customer'); 

        if ($id) {
            unset($rules['email']);
        }

        return $rules;
    }
}