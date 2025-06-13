<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomRoomPricingRequest;
use App\Models\RoomPricing;
use App\Services\RoomPricingService;
use Illuminate\Http\Request;

class RoomPricingController extends Controller
{
    private $roomPricingService;

    public function __construct(RoomPricingService $roomPricingService){   
        $this->roomPricingService = $roomPricingService;
    }
    
    public function index(Request $request)
    {

        $data = $this->roomPricingService->getAll();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function create()
    {
    }

    public function store(CustomRoomPricingRequest $request)
    {
        $roomPricing = $this->roomPricingService->create($request->validated());
        return response()->json([
            'data' => $roomPricing,
            'message' => 'Tạo mới giá loại phòng thành công'
        ], 201);
    }

    public function show($id)
    {
        $roomPricing = $this->roomPricingService->getById($id);
        return response()->json([
            'data' => $roomPricing
        ], 200);
    }

    public function edit(RoomPricing $customerType)
    {
        //
    }

    public function update(CustomRoomPricingRequest $request, string $id)
    {
        $data = $request->validated();
        $roomPricing = $this->roomPricingService->update($id, $data);
        return response()->json([
            'data' => $roomPricing,
            'message' => 'Cập nhật giá loại phòng thành công'
        ]);
    }
    
    public function destroy($id)
    {
        $this->roomPricingService->delete($id);
        return response()->json([
            'message' => 'Xóa giá loại phòng thành công'
        ], 200);
    }

}