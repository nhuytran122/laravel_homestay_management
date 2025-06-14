<?php

namespace App\Http\Requests;

use App\Services\ExtraServiceService;
use Illuminate\Foundation\Http\FormRequest;

class BookingServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'services' => 'required|array',
            'services.*.serviceId' => 'required|exists:services,id',
            'services.*.quantity' => 'required|numeric|min:1',
            'services.*.description' => 'nullable|string|max:255',
        ];
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