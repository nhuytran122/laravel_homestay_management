<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $customerService;

    public function __construct(CustomerService $customerService){   
        $this->customerService = $customerService;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $data = $keyword
            ? $this->customerService->search($keyword)
            : $this->customerService->getAll();

        return response()->json([
            'data' => $data,
            'message' => $keyword && $data->isEmpty()
                ? 'Không tìm thấy khách hàng nào phù hợp.'
                : null
        ]);
    }


    public function create()
    {
    }

    public function store(CustomerRequest $request)
    {
        $validated = $request->validated(); 
        $customer = $this->customerService->create($validated);
        return response()->json([
            'data' => $customer,
            'message' => 'Tạo mới khách hàng thành công'
        ], 201);
    }

    public function show($id)
    {
        $customer = $this->customerService->getById($id);
        return response()->json([
            'data' => $customer
        ], 200);
    }

    public function edit(Customer $customerType)
    {
        //
    }

    public function update(CustomerRequest $request, string $id)
    {
        $customer = $this->customerService->update($id, $request->validated());
        return response()->json([
            'data' => $customer,
            'message' => 'Cập nhật khách hàng thành công'
        ]);
    }

    public function destroy($id)
    {
        $this->customerService->delete($id);
        return response()->json([
            'message' => 'Xóa khách hàng thành công'
        ], 200);
    }

}