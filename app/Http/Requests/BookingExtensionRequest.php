<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use App\Services\RoomStatusHistoryService;
use App\Traits\InteractsWithDatetimeInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class BookingExtensionRequest extends FormRequest
{
    use InteractsWithDatetimeInput;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_check_out' => [
                'required',
                'date',
                'after:now',
                // function ($attribute, $value, $fail) {
                //     $booking = $this->route('booking');
                //     if ($booking && Carbon::parse($value)->lte($booking->check_out)) {
                //         $fail('Thời gian gia hạn phải sau thời gian checkout hiện tại.');
                //     }
                // }
            ],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('new_check_out')) {
            $this->convertDatetimeInputs(['new_check_out']);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $datetimeParseErrors = $this->datetimeParseErrors;
            if(!empty($datetimeParseErrors)){
                foreach ($this->datetimeParseErrors ?? [] as $field => $message) {
                    $v->errors()->add($field, $message);
                }
                return;
            }
            
            $booking = $this->route('booking');
            $newCheckOut = Carbon::parse($this->new_check_out);

            $isOverlapping = app(RoomStatusHistoryService::class)
                ->isOverlappingRoomWithExtension($booking, $newCheckOut);

            if ($isOverlapping) {
                $v->errors()->add('new_check_out', 'Thời gian bạn chọn đã có lịch phòng khác. Vui lòng chọn thời gian khác.');
                return;
            }
        });
    }

    public function toExtensionPayload(): array
    {
        $booking = $this->route('booking');

        return [
            'room_id' => $booking->room_id,
            'check_in' => $booking->check_out,
            'check_out' => $this->input('new_check_out'),
            'guest_count' => $booking->guest_count,
            'customer_id' => $booking->customer_id,
            'parent_id' => $booking->id,
            'status' => BookingStatus::PENDING_PAYMENT
        ];
    }

}