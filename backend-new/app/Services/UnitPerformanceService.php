<?php

namespace App\Services;

use App\Repositories\UnitPerformanceRepository;
use App\Repositories\BuildingRepository;
use App\Models\Building;
use Illuminate\Support\Facades\Cache;

class UnitPerformanceService extends BaseService
{
    protected $unitPerformanceRepository;
    protected $buildingRepository;

    public function __construct(UnitPerformanceRepository $unitPerformanceRepository, BuildingRepository $buildingRepository)
    {
        parent::__construct($unitPerformanceRepository);
        $this->unitPerformanceRepository = $unitPerformanceRepository;
        $this->buildingRepository = $buildingRepository;
    }

    /**
     * Get unit performance data dynamically generated from Building data
     *
     * @return array
     */
    public function getUnitPerformance()
    {
        // Ultra-fast cache with 0.5 second TTL for maximum responsiveness
        return Cache::remember('unit_performance', 0.5, function () {
            // Get building data with related rooms and CCTVs
            $buildings = \App\Models\Building::with(['rooms.cctvs'])->get();
            
            // If no building data exists, return empty array
            if ($buildings->isEmpty()) {
                return [];
            }
            
            // Transform the building data to match the expected format with realistic metrics
            return $buildings->map(function ($building) {
                // Calculate infrastructure counts
                $roomCount = $building->rooms->count();
                $cctvCount = $building->rooms->sum(function ($room) {
                    return $room->cctvs->count();
                });
                
                // Generate realistic performance metrics based on infrastructure
                $efficiency = min(100, max(70, 70 + ($roomCount * 2) + ($cctvCount * 0.5) + mt_rand(-10, 10)));
                $trafficDensity = max(0, 50 + ($cctvCount * 3) + mt_rand(-20, 20));
                $signalOptimization = min(100, max(60, 60 + ($roomCount * 1.5) + mt_rand(-15, 15)));
                $capacity = 500 + ($roomCount * 100) + ($cctvCount * 25);
                
                return [
                    'unit' => $building->name,
                    'efficiency' => round($efficiency),
                    'traffic_density' => round($trafficDensity),
                    'signal_optimization' => round($signalOptimization),
                    'capacity' => round($capacity),
                ];
            })->toArray();
        });
    }
}