<?php

namespace App\Services;

use App\Repositories\StatsRepository;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;
use Illuminate\Support\Facades\Cache;

class StatsService extends BaseService
{
    protected $statsRepository;

    public function __construct(StatsRepository $statsRepository)
    {
        parent::__construct($statsRepository);
        $this->statsRepository = $statsRepository;
    }

    public function getStats()
    {
        // Ultra-aggressive caching for stats - 5 seconds for maximum performance
        return Cache::remember('dashboard_stats', 5, function () {
            // Use raw database queries for maximum performance
            return [
                'total_buildings' => Building::count(),
                'total_rooms' => Room::count(),
                'total_cctvs' => Cctv::count(),
            ];
        });
    }
}
