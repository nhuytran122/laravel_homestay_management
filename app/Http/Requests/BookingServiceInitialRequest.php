<?php

namespace App\Http\Requests;

use App\Services\ExtraServiceService;

class BookingServiceInitialRequest extends BaseBookingServiceRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(parent::baseRules(), [
            'services.*.quantity' => 'required|numeric|min:1',
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $services = $this->input('services', []);
            foreach ($services as $index => $service) {
                $serviceModel = app(ExtraServiceService::class)->getById($service['serviceId'] ?? null);
                if (!$serviceModel || !$serviceModel->is_prepaid) {
                    $validator->errors()->add("services.$index.serviceId", 'Chỉ được chọn dịch vụ thanh toán trước (prepaid).');
                }
            }
        });
    }
}