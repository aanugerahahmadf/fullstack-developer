<?php

namespace App\Filament\Widgets;

use App\Models\Cctv;
use App\Models\Room;
use App\Models\Building;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
   
    protected function getStats(): array
    {
        $totalBuildings = Building::count();

        $totalRooms = Room::count();

        $totalCctvs = Cctv::count();

        // Calculate growth percentages (mock data for demonstration)
        $buildingGrowth = rand(5, 15);
        $roomGrowth = rand(3, 12);
        $cctvGrowth = rand(8, 20);

        return [
            Stat::make('Total Buildings', $totalBuildings)
                ->description($buildingGrowth . '% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([7, 2, 5, 3, 9, 5, $totalBuildings])
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Total Rooms', $totalRooms)
                ->description($roomGrowth . '% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([4, 1, 6, 2, 8, 4, $totalRooms])
                ->icon('heroicon-o-home'),

            Stat::make('Total CCTV', $totalCctvs)
                ->description($cctvGrowth . '% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([2, 4, 3, 7, 5, 6, $totalCctvs])
                ->icon('heroicon-o-video-camera'),
        ];
    }
}