<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class NetworkTrafficChart extends ChartWidget
{
    protected ?string $heading = 'Network Traffic';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Incoming Traffic (Mbps)',
                    'data' => [45, 52, 48, 61, 55, 67, 58],
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Outgoing Traffic (Mbps)',
                    'data' => [32, 38, 35, 42, 39, 45, 40],
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
