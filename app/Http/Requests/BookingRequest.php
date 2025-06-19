<?php

namespace App\Http\Requests;

use App\Enums\RoleSystem;
use App\Services\RoomService;
use App\Services\RoomStatusHistoryService;
use App\Traits\InteractsWithDatetimeInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class BookingRequest extends FormRequest
{
    use InteractsWithDatetimeInput;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'room_id'     => 'required|exists:rooms,id',
            'check_in'    => 'required|date|after:now',
            'check_out'   => 'required|date|after:check_in',
            'guest_count' => 'required|numeric|min:1',
        ];
        if($this->user()->hasAnyRole([RoleSystem::MANAGER->value, RoleSystem::STAFF->value]))
            $rules['customer_id'] = 'required|exists:customers,id';

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->filled('check_in')) {
            $this->convertDatetimeInputs(['check_in']);
        }

        if ($this->filled('check_out')) {
            $this->convertDatetimeInputs(['check_out']);
        }
    }

    
    public function withValidator($validator){
        $validator->after(function ($v){
            $datetimeParseErrors = $this->datetimeParseErrors;
            if(!empty($datetimeParseErrors)){
                foreach ($this->datetimeParseErrors ?? [] as $field => $message) {
                    $v->errors()->add($field, $message);
                }
                return;
            }
            $input_room_id = $this->input('room_id');
            $input_guests = $this->input('guest_count');
            $input_checkin = $this->input('check_in');
            $input_checkout = $this->input('check_out');

            if (!$input_room_id || !$input_guests || !$input_checkin || !$input_checkout) {
                return;
            }
            $room = app(RoomService::class)->getById($input_room_id);
            $max_guest = $room->room_type->max_guest;

            if($input_guests > $max_guest){
                $v->errors()->add('guest_count', 'Số khách cư trú vượt quá cho phép');
                return;
            }

            $isOverlapping = app(RoomStatusHistoryService::class)->existsOverlappingStatuses($input_room_id, $input_checkin, $input_checkout);

            if($isOverlapping){
                $v->errors()->add('check_in', 'Phòng này đã có lịch trong thời gian bạn chọn, vui lòng chọn thời gian khác hoặc phòng khác');
                return;
            }
        }); 
    }

}