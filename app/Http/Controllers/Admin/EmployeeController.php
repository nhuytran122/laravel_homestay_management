<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    private $employeeService;

    public function __construct(EmployeeService $employeeService){   
        $this->employeeService = $employeeService;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $data = $keyword
            ? $this->employeeService->search($keyword)
            : $this->employeeService->getAll();

        return response()->json([
            'data' => $data,
            'message' => $keyword && $data->isEmpty()
                ?? 'Không tìm thấy nhân viên nào phù hợp.'
        ]);
    }


    public function create()
    {
    }

    public function store(EmployeeRequest $request)
    {
        $validated = $request->validated(); 
        $employee = $this->employeeService->create($validated);
        return response()->json([
            'data' => $employee,
            'message' => 'Tạo mới nhân viên thành công'
        ], 201);
    }

    public function show($id)
    {
        $employee = $this->employeeService->getById($id);
        return response()->json([
            'data' => $employee
        ], 200);
    }

    public function edit(Employee $customerType)
    {
        //
    }

    public function update(EmployeeRequest $request, string $id)
    {
        $employee = $this->employeeService->update($id, $request->validated());
        return response()->json([
            'data' => $employee,
            'message' => 'Cập nhật nhân viên thành công'
        ]);
    }

    public function destroy($id)
    {
        $this->employeeService->delete($id);
        return response()->json([
            'message' => 'Xóa nhân viên thành công'
        ], 200);
    }

}