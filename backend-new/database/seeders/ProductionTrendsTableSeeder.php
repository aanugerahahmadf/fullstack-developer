<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductionTrend;

class ProductionTrendsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        ProductionTrend::truncate();
        
        // No sample data is created since backend data hasn't been set up yet
        // When ready, add production trends data here
    }
}
