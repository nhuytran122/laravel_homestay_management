<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('room');

        return [
            'room_number' => [
                'required',
                'numeric',
                'min:1',
                Rule::unique('rooms', 'room_number')
                    ->ignore($id)
                    ->where(function ($query) {
                        return $query->where('branch_id', $this->branch_id);
                    }),
            ],
            'area' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'room_type_id' => 'required|exists:room_types,id',
            'image' => 'nullable|file|mimes:jpg,png|max:2048',
        ];
    }

    
}