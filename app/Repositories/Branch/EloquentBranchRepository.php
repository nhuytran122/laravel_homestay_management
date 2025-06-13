<?php
namespace App\Repositories\Branch;
use App\Models\Branch;

    class EloquentBranchRepository implements BranchRepositoryInterface{
        public function findById($id)
        {
            return Branch::find($id); 
        }

        public function search(string $keyword)
        {
            return Branch::where('name', 'like', '%' . $keyword . '%')
                ->orWhere('address', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%')
                ->get();
        }

        public function getAll()
        {
            return Branch::all();
        }
        
        public function create($data)
        {
            return Branch::create($data);
        }

        public function update($id, $data)
        {
            $branch = $this->findById($id);
            $branch->update($data);
            return $branch;
        }

        public function delete($id)
        {
            $branch = $this->findById($id);
            return $branch->delete();
        }

    }