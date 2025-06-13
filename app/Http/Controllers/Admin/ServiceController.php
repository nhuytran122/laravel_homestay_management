<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Services\ExtraServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private $serviceService;

    public function __construct(ExtraServiceService $serviceService){   
        $this->serviceService = $serviceService;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $data = $keyword
            ? $this->serviceService->search($keyword)
            : $this->serviceService->getAll();

        return response()->json([
            'data' => $data,
            'message' => $keyword && $data->isEmpty()
                ? 'Không tìm thấy dịch vụ nào phù hợp.'
                : null
        ]);
    }

    public function create()
    {
    }

    public function store(ServiceRequest $request)
    {
        $service = $this->serviceService->create($request->validated());
        return response()->json([
            'data' => $service,
            'message' => 'Tạo mới dịch vụ thành công'
        ], 201);
    }

    public function show($id)
    {
        $service = $this->serviceService->getById($id);
        return response()->json([
            'data' => $service
        ], 200);
    }

    public function edit(Service $service)
    {
        //
    }

    public function update(ServiceRequest $request, string $id)
    {
        $service = $this->serviceService->update($id, $request->validated());
        return response()->json([
            'data' => $service,
            'message' => 'Cập nhật dịch vụ thành công'
        ]);
    }
    
    public function destroy($id)
    {
        $this->serviceService->delete($id);
        return response()->json([
            'message' => 'Xóa dịch vụ thành công'
        ], 200);
    }

}