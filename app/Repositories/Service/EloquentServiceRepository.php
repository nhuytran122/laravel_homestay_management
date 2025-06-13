<?php
namespace App\Repositories\Service;
use App\Models\Service;

    class EloquentServiceRepository implements ServiceRepositoryInterface{
        public function findById($id)
        {
            return Service::find($id); 
        }

        public function search(string $keyword)
        {
            return Service::where('name', 'like', '%' . $keyword . '%')
                ->get();
        }

        public function getAll()
        {
            return Service::all();
        }
        
        public function create($data)
        {
            return Service::create($data);
        }

        public function update($id, $data)
        {
            $service = $this->findById($id);
            $service->update($data);
            return $service;
        }

        public function delete($id)
        {
            $service = $this->findById($id);
            return $service->delete();
        }

    }