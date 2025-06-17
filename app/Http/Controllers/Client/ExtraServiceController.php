<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ExtraServiceService;
use Illuminate\Http\Request;

class ExtraServiceController extends Controller{
    private ExtraServiceService $extraServiceService;

    public function __construct(ExtraServiceService $extraServiceService){
        $this->extraServiceService = $extraServiceService;
    }

    public function getServicesByType(Request $request){
        $type = $request->input('type');
        if (!in_array($type, ['prepaid', 'postpaid'])) {
            return response()->json(['message' => 'Loại dịch vụ không hợp lệ'], 400);
        }
        $isPrepaid = $type === 'prepaid';
        $services = $this->extraServiceService->getListServicesByIsPrepaid($isPrepaid);
        return response()->json([
            'services' => $services
        ]);
    }

    public function getAll(){
        return response()->json([
            'services' => $this->extraServiceService->getAll()
        ]);
    }
}