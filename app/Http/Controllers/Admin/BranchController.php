<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    private $branchService;

    public function __construct(BranchService $branchService){   
        $this->branchService = $branchService;
    }
    
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $data = $keyword
            ? $this->branchService->search($keyword)
            : $this->branchService->getAll();

        return response()->json([
            'data' => $data,
            'message' => $keyword && $data->isEmpty()
                ? 'Không tìm thấy chi nhánh nào phù hợp.'
                : null
        ]);
    }

    public function create()
    {
    }

    public function store(BranchRequest $request)
    {
        $branch = $this->branchService->create($request->validated());
        if ($request->hasFile('image')) {
            $branch->addMediaFromRequest('image')->toMediaCollection('images');
        }
        return response()->json([
            'data' => $branch,
            'message' => 'Tạo mới chi nhánh thành công'
        ], 201);
    }

    public function show($id)
    {
        $branch = $this->branchService->getById($id);
        return response()->json([
            'data' => $branch
        ], 200);
    }

    public function edit(Branch $customerType)
    {
        //
    }

    public function update(BranchRequest $request, string $id)
    {
        $branch = $this->branchService->update($id, $request->validated());
        if ($request->hasFile('image')) {
            $branch->clearMediaCollection('images');
            $branch->addMediaFromRequest('image')->toMediaCollection('images');
        }
        return response()->json([
            'data' => $branch,
            'message' => 'Cập nhật chi nhánh thành công'
        ]);
    }
    
    public function destroy($id)
    {
        $this->branchService->delete($id);
        return response()->json([
            'message' => 'Xóa chi nhánh thành công'
        ], 200);
    }

}