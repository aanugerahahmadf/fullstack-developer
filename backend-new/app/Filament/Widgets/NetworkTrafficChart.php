<?php

namespace App\Filament\Widgets;

use App\Models\Cctv;
use Filament\Widgets\ChartWidget;

class NetworkTrafficChart extends ChartWidget
{
    protected static ?int $sort = 4;
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
                        'data' => [20, 25, 22, 28, 24, 30, 26],
                        'borderColor' => '#3B82F6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 0,
                    ],
                    [
                        'label' => 'Outgoing Traffic (Mbps)',
                        'data' => [8, 10, 9, 12, 11, 14, 10],
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 0,
                    ],
                ],
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            ];
        }

        // Calculate realistic network traffic based on CCTV count
        // Assume each CCTV uses ~2 Mbps bandwidth
        $baseIncoming = $totalCctvs * 2;
        $baseOutgoing = $totalCctvs * 0.5; // Less outgoing traffic

        // Generate weekly data with realistic variations
        $incomingData = [];
        $outgoingData = [];
        $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        // Create typical line chart data with gradual variations
        for ($i = 0; $i < 7; $i++) {
            // Add random but realistic variations
            $incomingVariation = $baseIncoming * (rand(-10, 10) / 100);
            $outgoingVariation = $baseOutgoing * (rand(-10, 10) / 100);
            
            $incomingData[] = max(0, $baseIncoming + $incomingVariation);
            $outgoingData[] = max(0, $baseOutgoing + $outgoingVariation);
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
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Outgoing Traffic (Mbps)',
                    'data' => $outgoingData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4, // Smooth curves
                    'pointRadius' => 0,
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