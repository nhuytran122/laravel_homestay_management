<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerTypeRequest;
use App\Models\CustomerType;
use App\Services\CustomerTypeService;
use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    private $customerTypeService;

    public function __construct(CustomerTypeService $customerTypeService){   
        $this->customerTypeService = $customerTypeService;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $data = $keyword
            ? $this->customerTypeService->searchByName($keyword)
            : $this->customerTypeService->getAll();

        return response()->json([
            'data' => $data,
            'message' => $keyword && $data->isEmpty()
                ? 'Không tìm thấy phân loại khách hàng nào phù hợp.'
                : null
        ]);
    }


    public function create()
    {
    }

    public function store(CustomerTypeRequest $request)
    {
        $customer_type = $this->customerTypeService->create($request->validated());
        return response()->json([
            'data' => $customer_type,
            'message' => 'Tạo mới phân loại khách hàng thành công'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customer_type = $this->customerTypeService->getById($id);
        return response()->json([
            'data' => $customer_type
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerType $customerType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerTypeRequest $request, string $id)
    {
        $customer_type = $this->customerTypeService->update($id, $request->validated());
        return response()->json([
            'data' => $customer_type,
            'message' => 'Cập nhật phân loại khách hàng thành công'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->customerTypeService->delete($id);
        return response()->json([
            'message' => 'Xóa phân loại khách hàng thành công'
        ], 200);
    }

}