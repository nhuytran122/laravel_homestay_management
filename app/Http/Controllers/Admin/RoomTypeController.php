<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomTypeRequest;
use App\Models\RoomType;
use App\Services\RoomTypeService;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    private $roomTypeService;

    public function __construct(RoomTypeService $roomTypeService){   
        $this->roomTypeService = $roomTypeService;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $data = $keyword
            ? $this->roomTypeService->search($keyword)
            : $this->roomTypeService->getAll();

        return response()->json([
            'data' => $data,
            'message' => $keyword && $data->isEmpty()
                ? 'Không tìm thấy loại phòng nào phù hợp.'
                : null
        ]);
    }

    public function create()
    {
    }

    public function store(RoomTypeRequest $request)
    {
        $room_type = $this->roomTypeService->create(
            $request->validated(),
            $request->file('image')
        );
        return response()->json([
            'data' => $room_type,
            'message' => 'Tạo mới phòng thành công'
        ], 201);
    }

    public function show($id)
    {
        $room_type = $this->roomTypeService->getById($id);
        return response()->json([
            'data' => $room_type
        ], 200);
    }

    public function edit(RoomType $room_type)
    {
        //
    }

    public function update(RoomTypeRequest $request, string $id)
    {
        $room_type = $this->roomTypeService->update($id, $request->validated(), 
                                                    $request->file('image'));
        return response()->json([
            'data' => $room_type,
            'message' => 'Cập nhật loại phòng thành công'
        ]);
    }
    
    public function destroy($id)
    {
        $this->roomTypeService->delete($id);
        return response()->json([
            'message' => 'Xóa loại phòng thành công'
        ], 200);
    }

}