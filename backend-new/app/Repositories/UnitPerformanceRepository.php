<?php

namespace App\Repositories;

use App\Models\UnitPerformance;
use App\Repositories\BaseRepository;

class UnitPerformanceRepository extends BaseRepository
{
    public function __construct(UnitPerformance $model)
    {
        parent::__construct($model);
    }

    // Add specific methods for UnitPerformance repository here if needed
    
    /**
     * Get all unit performance data ordered by efficiency
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllOrderedByEfficiency()
    {
        return $this->model->orderBy('efficiency', 'desc')->get();
    }
}