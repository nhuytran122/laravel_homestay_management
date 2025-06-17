<?php

namespace App\Http\Requests;

use App\Services\ExtraServiceService;

class BookingServicePostConfirmedRequest extends BaseBookingServiceRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(parent::baseRules(), [
            'services.*.quantity' => 'nullable|numeric|min:1',
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->input('services', []) as $index => $service) {
                $serviceModel = app(ExtraServiceService::class)->getById($service['serviceId'] ?? null);
                if (!$serviceModel) {
                    continue;
                }
                // Nếu là prepaid -> phải có quantity
                if ($serviceModel->is_prepaid && empty($service['quantity'])) {
                    $validator->errors()->add("services.$index.quantity", 'Với dịch vụ thanh toán trước, vui lòng nhập số lượng.');
                }
            }
        });
    }
}