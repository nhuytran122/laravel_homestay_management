<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use App\Services\RoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    private $roomService;

    public function __construct(RoomService $roomService){   
        $this->roomService = $roomService;
    }
    
    public function index(Request $request)
    {
        $room_type_id = $request->input('room_type_id');
        $branch_id = $request->input('branch_id');

        $data = $this->roomService->search($room_type_id, $branch_id);

        return response()->json([
            'data' => $data,
            'message' => $data->isEmpty()
                ? 'Không tìm thấy phòng nào phù hợp.'
                : null
        ]);
    }


    public function create()
    {
    }

    public function store(RoomRequest $request)
    {
        $room = $this->roomService->create($request->validated(),
                                            $request->file('image'));
        return response()->json([
            'data' => $room,
            'message' => 'Tạo mới phòng thành công'
        ], 201);
    }

    public function show($id)
    {
        $room = $this->roomService->getById($id);
        return response()->json([
            'data' => $room
        ], 200);
    }

    public function edit(Room $room)
    {
        //
    }

    public function update(RoomRequest $request, string $id)
    {
        $room = $this->roomService->update($id, $request->validated(),
                                            $request->file('image'));
        return response()->json([
            'data' => $room,
            'message' => 'Cập nhật phòng thành công'
        ]);
    }
    
    public function destroy($id)
    {
        $this->roomService->delete($id);
        return response()->json([
            'message' => 'Xóa phòng thành công'
        ], 200);
    }

}