<?php

use App\Models\ProductionTrend;
use App\Models\UnitPerformance;

// Clear existing data
ProductionTrend::truncate();
UnitPerformance::truncate();

// Add sample production trends data for November 2025
$productionTrends = [
    [
        'date' => '2025-11-01',
        'production' => 1200.00,
        'target' => 1500.00,
        'traffic_volume' => 25000.00,
        'average_speed' => 45.50,
        'incidents' => 3,
        'congestion_index' => 65.20,
        'signal_changes' => 1200,
        'green_wave_efficiency' => 72.50
    ],
    [
        'date' => '2025-11-05',
        'production' => 1400.00,
        'target' => 1500.00,
        'traffic_volume' => 28000.00,
        'average_speed' => 48.20,
        'incidents' => 2,
        'congestion_index' => 58.70,
        'signal_changes' => 1350,
        'green_wave_efficiency' => 78.30
    ],
    [
        'date' => '2025-11-10',
        'production' => 1100.00,
        'target' => 1500.00,
        'traffic_volume' => 22000.00,
        'average_speed' => 42.80,
        'incidents' => 5,
        'congestion_index' => 72.40,
        'signal_changes' => 1100,
        'green_wave_efficiency' => 68.90
    ],
    [
        'date' => '2025-11-15',
        'production' => 1600.00,
        'target' => 1500.00,
        'traffic_volume' => 32000.00,
        'average_speed' => 52.10,
        'incidents' => 1,
        'congestion_index' => 45.30,
        'signal_changes' => 1600,
        'green_wave_efficiency' => 85.20
    ],
    [
        'date' => '2025-11-20',
        'production' => 1300.00,
        'target' => 1500.00,
        'traffic_volume' => 26000.00,
        'average_speed' => 46.70,
        'incidents' => 4,
        'congestion_index' => 62.80,
        'signal_changes' => 1250,
        'green_wave_efficiency' => 75.60
    ],
    [
        'date' => '2025-11-25',
        'production' => 1550.00,
        'target' => 1500.00,
        'traffic_volume' => 30000.00,
        'average_speed' => 50.30,
        'incidents' => 2,
        'congestion_index' => 55.10,
        'signal_changes' => 1450,
        'green_wave_efficiency' => 82.40
    ],
    [
        'date' => '2025-11-30',
        'production' => 1450.00,
        'target' => 1500.00,
        'traffic_volume' => 29000.00,
        'average_speed' => 49.80,
        'incidents' => 3,
        'congestion_index' => 58.90,
        'signal_changes' => 1400,
        'green_wave_efficiency' => 80.70
    ]
];

foreach ($productionTrends as $trend) {
    ProductionTrend::create($trend);
}

echo "Production trends data added successfully!\n";

// Add sample unit performance data
$unitPerformances = [
    [
        'unit_name' => 'Central Traffic Hub',
        'efficiency' => 85,
        'capacity' => 1000
    ],
    [
        'unit_name' => 'Highway Interchange',
        'efficiency' => 92,
        'capacity' => 1200
    ],
    [
        'unit_name' => 'Downtown Core',
        'efficiency' => 78,
        'capacity' => 800
    ],
    [
        'unit_name' => 'Residential Area',
        'efficiency' => 95,
        'capacity' => 1500
    ],
    [
        'unit_name' => 'Industrial Zone',
        'efficiency' => 88,
        'capacity' => 1100
    ]
];

foreach ($unitPerformances as $performance) {
    UnitPerformance::create($performance);
}

echo "Unit performance data added successfully!\n";