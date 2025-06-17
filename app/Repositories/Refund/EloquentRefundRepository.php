<?php
namespace App\Repositories\Refund;

use App\Enums\RefundStatus;
use App\Enums\RefundType;
use App\Models\Refund;

    class EloquentRefundRepository implements RefundRepositoryInterface{
        public function findById($id)
        {
            return Refund::find($id); 
        }

        public function search(?array $filters, int $perPage = 10)
        {
            $query = $this->getAll();

            if (!empty($filters['from']) && !empty($filters['to'])) {
                $query->whereBetween('created_at', [$filters['from'], $filters['to']]);
            }

            if (!empty($filters['type']) && in_array($filters['type'], RefundType::values())) {
                $query->where('type', $filters['type']);
            }
            if (!empty($filters['status']) && in_array($filters['status'], RefundStatus::values())) {
                $query->where('status', $filters['status']);
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        }

        public function getAll()
        {
            return Refund::all();
        }
        
        public function create($data)
        {
            return Refund::create($data);
        }

        public function update($id, $data)
        {
            $refund = $this->findById($id);
            $refund->update($data);
            return $refund;
        }

        public function delete($id)
        {
            $refund = $this->findById($id);
            return $refund->delete();
        }

    }