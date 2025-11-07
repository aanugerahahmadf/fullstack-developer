<?php

namespace App\Filament\Widgets;

use App\Models\Cctv;
use Filament\Widgets\ChartWidget;

class CctvStatusTrendChart extends ChartWidget
{
    protected ?string $heading = 'Cctv Status Trend Chart';

    protected function getData(): array
    {
        $totalCctvs = Cctv::count();
        $onlineCount = $totalCctvs; // Placeholder - in production, check actual status
        $offlineCount = max(0, $totalCctvs - $onlineCount);
        return [
                        'datasets' => [
                [
                    'label' => 'Online Cameras',
                    'data' => [
                        $onlineCount,
                        $onlineCount - 1,
                        $onlineCount + 2,
                        $onlineCount,
                        $onlineCount + 1,
                        $onlineCount - 1,
                        $onlineCount,
                    ],
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Offline Cameras',
                    'data' => [
                        $offlineCount,
                        $offlineCount + 1,
                        max(0, $offlineCount - 2),
                        $offlineCount,
                        max(0, $offlineCount - 1),
                        $offlineCount + 1,
                        $offlineCount,
                    ],
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
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
