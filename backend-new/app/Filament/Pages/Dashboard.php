<?php

namespace App\Filament\Pages;

use UnitEnum;
use BackedEnum;
use Filament\Panel;
use Filament\Pages\Page;
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\NetworkTrafficChart;
use App\Filament\Widgets\CctvOperationalTable;
use App\Filament\Widgets\CctvStatusTrendChart;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StreamingPerformanceChart;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'bxs-dashboard';

    protected static string|UnitEnum|null $navigationGroup = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Widgets';

    public static function getRouteName(?Panel $panel = null): string
    {
        return parent::getRouteName($panel);
    }

    public static function getNavigationIcon(): ?string
    {
        return static::$navigationIcon;
    }

    public function getWidgets(): array
    {
        return [
            DashboardStats::class,
            CctvStatusTrendChart::class,
            StreamingPerformanceChart::class,
            NetworkTrafficChart::class,
            CctvOperationalTable::class,
        ];
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }
}
