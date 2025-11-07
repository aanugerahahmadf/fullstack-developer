<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class StreamingPerformanceChart extends ChartWidget
{
    protected ?string $heading = 'Streaming Performance';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Average FPS',
                    'data' => [29, 30, 28, 31, 30, 29, 30],
                    'borderColor' => '#8B5CF6',
                    'fill' => false,
                ],
                [
                    'label' => 'Latency (ms)',
                    'data' => [120, 115, 130, 110, 125, 140, 115],
                    'borderColor' => '#F59E0B',
                    'fill' => false,
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
