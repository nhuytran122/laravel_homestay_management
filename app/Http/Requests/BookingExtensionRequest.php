<?php

namespace App\Http\Requests;

use App\Services\RoomStatusHistoryService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class BookingExtensionRequest extends FormRequest
{
    public function authorize(): bool
    {
        //todo
        return true;
    }

    public function rules(): array
    {
        return [
            'new_check_out' => [
                'required',
                'date_format:Y-m-d H:i:s',
                'after:now',
                function ($attribute, $value, $fail) {
                    $booking = $this->route('booking');
                    if ($booking && Carbon::parse($value)->lte($booking->check_out)) {
                        $fail('Thời gian gia hạn phải sau thời gian checkout hiện tại.');
                    }
                }
            ],
            'extended_hours'  => 'nullable|numeric|min:0.5' // để validated() trả ra
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->filled('new_check_out')) {
            try {
                $newCheckout = Carbon::createFromFormat('H:i d/m/Y', $this->new_check_out);
                $formattedCheckout = $newCheckout->format('Y-m-d H:i:s');

                $booking = $this->route('booking');
                $extendedHours = null;

                if ($booking) {
                    $currentCheckOut = $booking->check_out;
                    $newCheckOutCarbon = Carbon::parse($formattedCheckout);

                    $minutesDelay = $currentCheckOut->diffInMinutes($newCheckOutCarbon, false);
                    $extendedHours = ceil($minutesDelay / 30.0) * 0.5;
                }

                $this->merge([
                    'new_check_out' => $formattedCheckout,
                    'extended_hours' => $extendedHours
                ]);
            } catch (\Exception $e) {
            }
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $booking = $this->route('booking');
            $extendedHours = $this->input('extended_hours');

            $isOverlapping = app(RoomStatusHistoryService::class)
                ->isOverlappingRoomWithExtension(
                    $booking, $extendedHours
                );

            if ($isOverlapping) {
                $v->errors()->add('new_check_out', 'Thời gian bạn chọn đã có lịch phòng khác. Vui lòng chọn thời gian khác.');
                return;
            }
        });
    }
}