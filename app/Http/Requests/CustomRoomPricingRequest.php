<?php

namespace App\Http\Requests;

use App\Helpers\DateHelper;
use App\Services\RoomPricingService;
use Illuminate\Support\Carbon;

class CustomRoomPricingRequest extends BaseRoomPricingRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'room_type_id' => 'required|exists:room_types,id',
            'is_default' => 'required|boolean', 
            'start_date' => 'nullable|date', 
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $isDefault = filter_var($this->input('is_default'), FILTER_VALIDATE_BOOLEAN);
            $start = $this->input('start_date');
            $end = $this->input('end_date');
            $roomTypeId = $this->input('room_type_id');
            $id = $this->route('room_pricing') ?? null;

            if (!$isDefault) {
                if (empty($start)) {
                    $v->errors()->add('start_date', 'Vui lòng nhập ngày bắt đầu');
                }

                if (empty($end)) {
                    $v->errors()->add('end_date', 'Vui lòng nhập ngày kết thúc');
                }

                if ($start && $end && $start > $end) {
                    $v->errors()->add('end_date', 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu');
                }

                if ($start && $end) {
                    $isOverlap = app(RoomPricingService::class)->isOverlapping($start, $end, $roomTypeId, $id);
                    if ($isOverlap) {
                        $v->errors()->add('start_date', 'Thời gian áp dụng bị trùng lặp');
                        $v->errors()->add('end_date', 'Thời gian áp dụng bị trùng lặp');
                    }
                }
            }
        });
    }

    protected function prepareForValidation()
    {
        $this->merge([
            // 'start_date' => DateHelper::toDateFormat($this->start_date),
            // 'end_date' => DateHelper::toDateFormat($this->end_date),

            'start_date' => Carbon::createFromFormat('d/m/Y', $this->start_date)->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d/m/Y', $this->end_date)->format('Y-m-d')
        ]);
    }

}