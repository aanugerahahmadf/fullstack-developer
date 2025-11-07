<?php

namespace App\Repositories;

use App\Models\Chart;
use App\Repositories\BaseRepository;

class ChartRepository extends BaseRepository
{
    public function __construct(Chart $model)
    {
        parent::__construct($model);
    }

    // Add specific methods for Chart repository here if needed
}
