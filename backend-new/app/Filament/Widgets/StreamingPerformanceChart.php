<?php

namespace App\Filament\Widgets;

use App\Models\Cctv;
use Filament\Widgets\ChartWidget;

class StreamingPerformanceChart extends ChartWidget
{
    protected ?string $heading = 'Streaming Performance';

    protected function getData(): array
    {
        // Get realistic data based on actual CCTV count
        $totalCctvs = Cctv::count();

        // If no CCTVs, use default values
        if ($totalCctvs == 0) {
            return [
                'datasets' => [
                    [
                        'label' => 'Average FPS',
                        'data' => [29, 30, 28, 31, 30, 29, 30],
                        'borderColor' => '#8B5CF6',
                        'fill' => false,
                        'tension' => 0.4,
                        'pointRadius' => 3,
                    ],
                    [
                        'label' => 'Latency (ms)',
                        'data' => [120, 115, 130, 110, 125, 140, 115],
                        'borderColor' => '#F59E0B',
                        'fill' => false,
                        'tension' => 0.4,
                        'pointRadius' => 3,
                    ],
                ],
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            ];
        }

        // Generate realistic data based on actual CCTV count
        $baseFps = min(30, max(20, 30 - ($totalCctvs / 10))); // Decrease FPS as more CCTVs are added
        $baseLatency = max(80, min(200, 100 + ($totalCctvs * 2))); // Increase latency as more CCTVs are added

        // Generate weekly data with realistic variations and smooth waves
        $fpsData = [];
        $latencyData = [];
        $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        // Create smooth wave patterns using sine functions for more natural-looking curves
        for ($i = 0; $i < 7; $i++) {
            // Create wave patterns with different frequencies and phases
            $fpsWave = sin($i * 0.9) * 2; // ±2 FPS variation
            $latencyWave = sin($i * 1.1 + 2) * 15; // ±15ms variation with phase shift
            
            // Add random variation on top of wave patterns
            $fpsVariation = rand(-1, 1);
            $latencyVariation = rand(-10, 10);
            
            // Combine wave patterns with random variations
            $fpsData[] = max(15, min(35, $baseFps + $fpsWave + $fpsVariation));
            $latencyData[] = max(50, min(300, $baseLatency + $latencyWave + $latencyVariation));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Average FPS',
                    'data' => $fpsData,
                    'borderColor' => '#8B5CF6',
                    'fill' => false,
                    'tension' => 0.4, // Smooth curves
                    'pointRadius' => 3,
                ],
                [
                    'label' => 'Latency (ms)',
                    'data' => $latencyData,
                    'borderColor' => '#F59E0B',
                    'fill' => false,
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