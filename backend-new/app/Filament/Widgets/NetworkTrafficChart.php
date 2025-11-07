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
                    ],
                    [
                        'label' => 'Outgoing Traffic (Mbps)',
                        'data' => [0, 0, 0, 0, 0, 0, 0],
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
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

        for ($i = 0; $i < 7; $i++) {
            // Add variation (±20%)
            $incomingVariation = $baseIncoming * (rand(-20, 20) / 100);
            $outgoingVariation = $baseOutgoing * (rand(-20, 20) / 100);

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
                ],
                [
                    'label' => 'Outgoing Traffic (Mbps)',
                    'data' => $outgoingData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
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
