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
        // Cache the stats data for 30 seconds to improve performance
        return Cache::remember('dashboard_stats', 30, function () {
            return [
                'total_buildings' => Building::count(),
                'total_rooms' => Room::count(),
                'total_cctvs' => Cctv::count(),
            ];
        });
    }
}
