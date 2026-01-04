<?php

namespace App\Services;

use App\Repositories\ProductionTrendRepository;
use Illuminate\Support\Facades\Cache;

class ProductionTrendService extends BaseService
{
    protected $productionTrendRepository;

    public function __construct(ProductionTrendRepository $productionTrendRepository)
    {
        parent::__construct($productionTrendRepository);
        $this->productionTrendRepository = $productionTrendRepository;
    }

    /**
     * Get production trends data dynamically generated from Building, Room, and CCTV data
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getProductionTrends($startDate = null, $endDate = null)
    {
        // Create cache key based on parameters
        $cacheKey = 'production_trends';
        if ($startDate) {
            $cacheKey .= '_from_' . $startDate;
        }
        if ($endDate) {
            $cacheKey .= '_to_' . $endDate;
        }

        // Ultra-fast cache with 0.5 second TTL for maximum responsiveness
        return Cache::remember($cacheKey, 0.5, function () use ($startDate, $endDate) {
            // Get building data to generate production trends
            $buildings = \App\Models\Building::with(['rooms.cctvs'])->get();
            
            // If no buildings exist, return empty array
            if ($buildings->isEmpty()) {
                return [];
            }
            
            // Determine date range - if not provided, use current month
            if (!$startDate) {
                $startDate = now()->startOfMonth()->format('Y-m-d');
            }
            if (!$endDate) {
                $endDate = now()->endOfMonth()->format('Y-m-d');
            }
            
            // Generate date range array
            $period = new \DatePeriod(
                new \DateTime($startDate),
                new \DateInterval('P1D'),
                (new \DateTime($endDate))->modify('+1 day')
            );
            
            $trends = [];
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                
                // Calculate metrics based on infrastructure
                $buildingCount = $buildings->count();
                $roomCount = $buildings->sum(function ($building) {
                    return $building->rooms->count();
                });
                $cctvCount = $buildings->sum(function ($building) {
                    return $building->rooms->sum(function ($room) {
                        return $room->cctvs->count();
                    });
                });
                
                // Generate realistic production values based on infrastructure
                $baseProduction = ($buildingCount * 100) + ($roomCount * 50) + ($cctvCount * 25);
                $randomFactor = mt_rand(80, 120) / 100; // 80-120% variance
                $production = round($baseProduction * $randomFactor);
                
                // Target is slightly higher than production
                $target = round($production * 1.1);
                
                // Generate ATCS metrics based on infrastructure
                $trafficVolume = $cctvCount * mt_rand(1000, 5000);
                $averageSpeed = mt_rand(30, 80);
                
                // Incidents calculation now heavily reliant on Room density (simulation)
                $incidents = mt_rand(0, max(1, floor(($cctvCount + $roomCount) / 5)));
                
                $congestionIndex = mt_rand(20, 80);
                
                // Signal changes correlated to number of Buildings (intersections)
                $signalChanges = $buildingCount * mt_rand(50, 200);
                
                $greenWaveEfficiency = mt_rand(60, 95);
                
                $trends[] = [
                    'date' => $dateStr,
                    'production' => $production,
                    'target' => $target,
                    'traffic_volume' => $trafficVolume,
                    'average_speed' => $averageSpeed,
                    'incidents' => $incidents,
                    'congestion_index' => $congestionIndex,
                    'signal_changes' => $signalChanges,
                    'green_wave_efficiency' => $greenWaveEfficiency,
                ];
            }
            
            return $trends;
        });
    }
}