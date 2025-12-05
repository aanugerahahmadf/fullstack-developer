<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UnitPerformance;

class UnitPerformanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        UnitPerformance::truncate();
        
        // Create sample unit performance data
        $unitPerformances = [
            ['unit_name' => 'Unit A', 'efficiency' => 85, 'capacity' => 1000],
            ['unit_name' => 'Unit B', 'efficiency' => 92, 'capacity' => 1200],
            ['unit_name' => 'Unit C', 'efficiency' => 78, 'capacity' => 800],
            ['unit_name' => 'Unit D', 'efficiency' => 95, 'capacity' => 1500],
            ['unit_name' => 'Unit E', 'efficiency' => 88, 'capacity' => 1100],
        ];
        
        // Insert the data
        foreach ($unitPerformances as $performance) {
            UnitPerformance::create($performance);
        }
    }
}
