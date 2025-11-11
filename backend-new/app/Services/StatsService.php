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
        // Ultra-fast cache with 0.5 second TTL for maximum responsiveness
        return Cache::remember('dashboard_stats', 0.5, function () {
            // Use raw database queries for maximum performance
            return [
                'total_buildings' => Building::count(),
                'total_rooms' => Room::count(),
                'total_cctvs' => Cctv::count(),
            ];
        });
    }
}
