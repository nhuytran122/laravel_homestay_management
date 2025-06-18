<?php
namespace App\Repositories\RoomType;

use App\Models\RoomPricing;
use App\Models\RoomType;
use App\Repositories\BaseEloquentRepository;

    class EloquentRoomTypeRepository extends BaseEloquentRepository implements RoomTypeRepositoryInterface{
        public function __construct()
        {
            $this->model = new RoomType();
        }        
        public function search(string $keyword)
        {
            return RoomType::where('name', 'like', '%' . $keyword . '%')
                ->get();
        }
    }