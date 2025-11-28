<?php

namespace App\Filament\Widgets;

use App\Models\Cctv;
use Filament\Widgets\ChartWidget;

class NetworkTrafficChart extends ChartWidget
{
    protected ?string $heading = 'Network Traffic';

    protected function getData(): array
    {
        // Get actual CCTV data to determine realistic network traffic
        $totalCctvs = Cctv::count();

        // If no CCTVs, use default values
        if ($totalCctvs == 0) {
            return [
                'datasets' => [
                    [
                        'label' => 'Incoming Traffic (Mbps)',
                        'data' => [0, 0, 0, 0, 0, 0, 0],
                        'borderColor' => '#3B82F6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 3,
                    ],
                    [
                        'label' => 'Outgoing Traffic (Mbps)',
                        'data' => [0, 0, 0, 0, 0, 0, 0],
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 3,
                    ],
                ],
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            ];
        }

        // Calculate realistic network traffic based on CCTV count
        // Assume each CCTV uses ~2 Mbps bandwidth
        $baseIncoming = $totalCctvs * 2;
        $baseOutgoing = $totalCctvs * 0.5; // Less outgoing traffic

        // Generate weekly data with realistic variations and smooth waves
        $incomingData = [];
        $outgoingData = [];
        $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        // Create smooth wave patterns using sine functions for more natural-looking curves
        for ($i = 0; $i < 7; $i++) {
            // Create wave patterns with different frequencies and phases
            $incomingWave = sin($i * 0.8) * ($baseIncoming * 0.15); // 15% variation
            $outgoingWave = sin($i * 1.2 + 1.5) * ($baseOutgoing * 0.2); // 20% variation with phase shift
            
            // Add random variation on top of wave patterns
            $incomingVariation = $baseIncoming * (rand(-10, 10) / 100);
            $outgoingVariation = $baseOutgoing * (rand(-10, 10) / 100);
            
            // Combine wave patterns with random variations
            $incomingData[] = max(0, $baseIncoming + $incomingWave + $incomingVariation);
            $outgoingData[] = max(0, $baseOutgoing + $outgoingWave + $outgoingVariation);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Incoming Traffic (Mbps)',
                    'data' => $incomingData,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4, // Smooth curves
                    'pointRadius' => 3,
                ],
                [
                    'label' => 'Outgoing Traffic (Mbps)',
                    'data' => $outgoingData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4, // Smooth curves
                    'pointRadius' => 3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getColumns(): int
    {
        return 6;
    }
}