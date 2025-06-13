<?php

namespace App\Http\Requests;

use App\Services\RoleService;
use Illuminate\Validation\Rule;

class EmployeeRequest extends BaseUserInfoRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerRoleId = app(RoleService::class)->getCustomerRoleId();
        $rules = parent::rules();

        $id = $this->route('employee'); 

        if ($id) {
            unset($rules['email']);
        }

        return array_merge($rules, [
            'salary' => 'required|numeric|min:0',
            'role_id' => [
                'required',
                'exists:roles,id',
                Rule::notIn([$customerRoleId]),
            ],
        ]);
    }
}