<?php

namespace App\Services;

use App\Repositories\ChartRepository;
use App\Models\Building;
use Illuminate\Support\Facades\Cache;

class ChartService extends BaseService
{
    protected $chartRepository;

    public function __construct(ChartRepository $chartRepository)
    {
        parent::__construct($chartRepository);
        $this->chartRepository = $chartRepository;
    }

    public function getProductionTrends()
    {
        // Cache the production trends data for 5 seconds to improve performance
        return Cache::remember('production_trends', 5, function () {
            // In a real application, this would retrieve actual chart data from the repository
            // For now, we'll return sample data but in the future this would come from the database
            // Using actual dates for the current month
            $currentMonth = date('Y-m');
            return [
                ['date' => $currentMonth . '-01', 'production' => 1200, 'target' => 1500],
                ['date' => $currentMonth . '-05', 'production' => 1400, 'target' => 1500],
                ['date' => $currentMonth . '-10', 'production' => 1100, 'target' => 1500],
                ['date' => $currentMonth . '-15', 'production' => 1600, 'target' => 1500],
                ['date' => $currentMonth . '-20', 'production' => 1300, 'target' => 1500],
                ['date' => $currentMonth . '-25', 'production' => 1550, 'target' => 1500],
                ['date' => $currentMonth . '-30', 'production' => 1450, 'target' => 1500],
            ];
        });
    }

    public function getUnitPerformance()
    {
        // Cache the unit performance data for 5 seconds to improve performance
        return Cache::remember('unit_performance', 5, function () {
            // Get actual building names from the database with optimized query
            $buildings = Building::select('name')->get();

            // If no buildings exist, return sample data
            if ($buildings->isEmpty()) {
                return [
                    ['unit' => 'Unit A', 'efficiency' => 85, 'capacity' => 1000],
                    ['unit' => 'Unit B', 'efficiency' => 92, 'capacity' => 1200],
                    ['unit' => 'Unit C', 'efficiency' => 78, 'capacity' => 800],
                    ['unit' => 'Unit D', 'efficiency' => 95, 'capacity' => 1500],
                    ['unit' => 'Unit E', 'efficiency' => 88, 'capacity' => 1100],
                ];
            }

            // Group buildings by name and count them
            $buildingCounts = [];
            foreach ($buildings as $building) {
                if (isset($buildingCounts[$building->name])) {
                    $buildingCounts[$building->name]++;
                } else {
                    $buildingCounts[$building->name] = 1;
                }
            }

            // Create performance data based on building names and counts
            $unitPerformance = [];
            $index = 0;
            foreach ($buildingCounts as $name => $count) {
                // If there are multiple buildings with the same name, show the count
                $displayName = ($count > 1) ? $name . ' ' . $count : $name;

                // Generate realistic efficiency values (percentages between 70-95)
                $efficiency = rand(70, 95);

                // Generate realistic capacity values (between 500-2000)
                $capacity = rand(500, 2000);

                $unitPerformance[] = [
                    'unit' => $displayName,
                    'efficiency' => $efficiency,
                    'capacity' => $capacity
                ];

                $index++;
            }

            return $unitPerformance;
        });
    }
}
