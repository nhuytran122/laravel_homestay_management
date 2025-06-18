<?php
namespace App\Repositories\Refund;

use App\Enums\RefundStatus;
use App\Enums\RefundType;
use App\Models\Refund;
use App\Repositories\BaseEloquentRepository;

    class EloquentRefundRepository extends BaseEloquentRepository implements RefundRepositoryInterface{
        public function __construct()
        {
            $this->model = new Refund();
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
    }