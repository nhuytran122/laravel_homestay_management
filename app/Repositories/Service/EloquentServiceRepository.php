<?php
namespace App\Repositories\Service;
use App\Models\Service;
use App\Repositories\BaseEloquentRepository;

    class EloquentServiceRepository extends BaseEloquentRepository implements ServiceRepositoryInterface{
        public function __construct()
        {
            $this->model = new Service();
        }

        public function search(string $keyword)
        {
            return Service::where('name', 'like', '%' . $keyword . '%')
                ->get();
        }

        public function findByIsPrepaid(bool $isPrepaid)
        {
            return Service::where('is_prepaid', $isPrepaid)->get();
        }

    }