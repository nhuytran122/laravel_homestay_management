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
            'new_check_out' => 'required|date_format:Y-m-d H:i:s|after:date:now',
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
                $hoursDelay = null;

                if ($booking) {
                    $currentCheckOut = Carbon::parse($booking->check_out);
                    $newCheckOutCarbon = Carbon::parse($formattedCheckout);

                    $minutesDelay = $currentCheckOut->diffInMinutes($newCheckOutCarbon, false);
                    $hoursDelay = ceil($minutesDelay / 30.0) * 0.5;
                }

                $this->merge([
                    'new_check_out' => $formattedCheckout,
                    'extended_hours' => $hoursDelay
                ]);
            } catch (\Exception $e) {
            }
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $booking = $this->route('booking');
            $bookingId = $booking->id;
            $newCheckOut = $this->input('new_check_out');

            $hoursDelay = null;

            if (!$bookingId || !$newCheckOut) {
                return;
            }
            $currentCheckOut = Carbon::parse($booking->check_out);
            $newCheckOutCarbon = Carbon::parse($newCheckOut);

            $CLEANING_HOURS = Config::get('custom.cleaning_hours'); 

            $checkOutWithBuffer = $currentCheckOut->copy()->addHours($CLEANING_HOURS);
            $newCheckOutWithBuffer = $newCheckOutCarbon->copy()->addHours($CLEANING_HOURS);

            $isOverlapping = app(RoomStatusHistoryService::class)
                ->existsOverlappingStatuses(
                    $booking->room_id,
                    $checkOutWithBuffer,
                    $newCheckOutWithBuffer
                );

            if ($isOverlapping) {
                $v->errors()->add('new_check_out', 'Thời gian bạn chọn đã có lịch phòng khác. Vui lòng chọn thời gian khác.');
                return;
            }

            $minutesDelay = $currentCheckOut->diffInMinutes($newCheckOutCarbon, false);
            $hoursDelay = ceil($minutesDelay / 30.0) * 0.5;

            if ($hoursDelay <= 0) {
                $v->errors()->add('new_check_out', 'Thời gian gia hạn phải sau thời gian checkout hiện tại.');
            }
        });
    }
}