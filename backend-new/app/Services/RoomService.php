<?php

namespace App\Services;

use App\Repositories\RoomRepository;
use Illuminate\Support\Facades\Cache;

class RoomService extends BaseService
{
    protected $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        parent::__construct($roomRepository);
        $this->roomRepository = $roomRepository;
    }

    public function getRoomsByBuildingId(int $buildingId)
    {
        // Cache the result for 30 seconds to reduce database load and improve response time
        return Cache::remember("rooms_by_building_{$buildingId}", 30, function () use ($buildingId) {
            return $this->roomRepository->getByBuildingId($buildingId);
        });
    }

    public function getRoomsWithCctvs()
    {
        // Cache the result for 30 seconds to reduce database load and improve response time
        return Cache::remember('rooms_with_cctvs', 30, function () {
            return $this->roomRepository->withCctvs();
        });
    }
}
