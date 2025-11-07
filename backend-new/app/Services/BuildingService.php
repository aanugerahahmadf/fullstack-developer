<?php

namespace App\Services;

use App\Repositories\BuildingRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class BuildingService extends BaseService
{
    protected $buildingRepository;

    public function __construct(BuildingRepository $buildingRepository)
    {
        parent::__construct($buildingRepository);
        $this->buildingRepository = $buildingRepository;
    }

    public function getBuildingsWithRoomsAndCctvs(): Collection
    {
        // Cache the result for 5 seconds to reduce database load and improve response time
        return Cache::remember('buildings_with_rooms_and_cctvs', 5, function () {
            return $this->buildingRepository->withRoomsAndCctvs();
        });
    }

    public function clearCache(): void
    {
        Cache::forget('buildings_with_rooms_and_cctvs');
        Cache::forget('dashboard_stats');
        Cache::forget('production_trends');
        Cache::forget('unit_performance');
    }
}
