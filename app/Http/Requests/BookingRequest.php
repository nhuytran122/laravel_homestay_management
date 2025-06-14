<?php

namespace App\Http\Requests;

use App\Enums\RoleSystem;
use App\Services\RoomService;
use App\Services\RoomStatusHistoryService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use PDO;
use PHPUnit\Event\Telemetry\System;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        //TODO: xử lý vs admin, chọn KH
        $rules = [
            'room_id'     => 'required|exists:rooms,id',
            'check_in'    => 'required|date_format:Y-m-d H:i:s|after:now',
            'check_out'   => 'required|date_format:Y-m-d H:i:s|after:check_in',
            'guest_count' => 'required|numeric|min:1',
        ];
        // if ($this->user()->hasRole([RoleSystem::MANAGER, RoleSystem::STAFF])) {
        //     $rules['customer_id'] = 'required|exists:customers,id';
        // }
        return $rules;
    }

    public function withValidator($validator){
        $validator->after(function ($v){
            $input_room_id = $this->input('room_id');
            $input_guests = $this->input('guest_count');
            $input_checkin = $this->input('check_in');
            $input_checkout = $this->input('check_out');

            // Nếu thiếu bất kỳ input nào thì bỏ qua custom validate
            if (!$input_room_id || !$input_guests || !$input_checkin || !$input_checkout) {
                return;
            }
            $room = app(RoomService::class)->getById($input_room_id);
            $max_guest = $room->room_type->max_guest;

            if($input_guests > $max_guest){
                $v->errors()->add('guest_count', 'Số khách cư trú vượt quá cho phép');
            }

            $input_checkin = $this->input('check_in');
            $input_checkout = $this->input('check_out');

            $isOverlapping = app(RoomStatusHistoryService::class)->existsOverlappingStatuses($input_room_id, $input_checkin, $input_checkout);

            if($isOverlapping){
                $v->errors()->add('check_in', 'Phòng này đã có lịch trong thời gian bạn chọn, vui lòng chọn thời gian khác hoặc phòng khác');
            }
        }); 
    }

    protected function prepareForValidation()
    {
        if ($this->filled('check_in') && $this->filled('check_out')) {
            try {
                $checkIn = Carbon::createFromFormat('H:i d/m/Y', $this->check_in);
                $checkOut = Carbon::createFromFormat('H:i d/m/Y', $this->check_out);

                $this->merge([
                    'check_in' => $checkIn->format('Y-m-d H:i:s'),
                    'check_out' => $checkOut->format('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
            }
        }
    }



}