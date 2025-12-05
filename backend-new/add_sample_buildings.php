<?php

use App\Models\Building;

// Clear existing buildings (optional)
// Building::truncate();

// Add sample buildings
$buildings = [
    [
        'name' => 'Central Traffic Hub',
        'latitude' => -6.39240000,
        'longitude' => 108.38147000,
        'marker_icon_url' => 'https://blade-ui-kit.com/blade-icons/govicon-building'
    ],
    [
        'name' => 'Highway Interchange',
        'latitude' => -6.39000000,
        'longitude' => 108.38000000,
        'marker_icon_url' => 'https://blade-ui-kit.com/blade-icons/govicon-building'
    ],
    [
        'name' => 'Downtown Core',
        'latitude' => -6.38800000,
        'longitude' => 108.38200000,
        'marker_icon_url' => 'https://blade-ui-kit.com/blade-icons/govicon-building'
    ],
    [
        'name' => 'Residential Area',
        'latitude' => -6.39500000,
        'longitude' => 108.37900000,
        'marker_icon_url' => 'https://blade-ui-kit.com/blade-icons/govicon-building'
    ],
    [
        'name' => 'Industrial Zone',
        'latitude' => -6.38500000,
        'longitude' => 108.38500000,
        'marker_icon_url' => 'https://blade-ui-kit.com/blade-icons/govicon-building'
    ]
];

foreach ($buildings as $buildingData) {
    Building::create($buildingData);
}

echo "Sample buildings added successfully!\n";