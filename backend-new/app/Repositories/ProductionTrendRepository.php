<?php

namespace App\Repositories;

use App\Models\ProductionTrend;
use App\Repositories\BaseRepository;

class ProductionTrendRepository extends BaseRepository
{
    public function __construct(ProductionTrend $model)
    {
        parent::__construct($model);
    }

    // Add specific methods for ProductionTrend repository here if needed
    
    /**
     * Get production trends within a date range
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrendsByDateRange($startDate = null, $endDate = null)
    {
        $query = $this->model->orderBy('date', 'asc');
        
        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }
        
        return $query->get();
    }
}