<?php
namespace App\Repositories\Branch;
use App\Models\Branch;
use App\Repositories\BaseEloquentRepository;

    class EloquentBranchRepository extends BaseEloquentRepository implements BranchRepositoryInterface{

        public function __construct()
        {
            $this->model = new Branch();
        }
        public function search(string $keyword)
        {
            return Branch::where('name', 'like', '%' . $keyword . '%')
                ->orWhere('address', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%')
                ->get();
        }
    }