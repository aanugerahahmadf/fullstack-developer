<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;


class CctvOperationalTable extends ChartWidget
{
    protected ?string $heading = 'Camera Operational Status';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Camera Status',
                    'data' => [124, 8, 3],
                    'backgroundColor' => [
                        '#10B981', // Green for online
                        '#F59E0B', // Yellow for warning
                        '#EF4444', // Red for offline
                    ],
                ],
            ],
            'labels' => ['Online', 'Warning', 'Offline'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
