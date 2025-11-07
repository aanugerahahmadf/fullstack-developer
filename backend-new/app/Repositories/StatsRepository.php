<?php

namespace App\Repositories;

use App\Models\Stats;
use App\Repositories\BaseRepository;

class StatsRepository extends BaseRepository
{
    public function __construct(Stats $model)
    {
        parent::__construct($model);
    }

    // Add specific methods for Stats repository here if needed
}
