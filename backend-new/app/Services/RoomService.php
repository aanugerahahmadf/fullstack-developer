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
        // Ultra-fast cache with 0.5 second TTL for maximum responsiveness
        return Cache::remember("rooms_by_building_{$buildingId}", 0.5, function () use ($buildingId) {
            return $this->roomRepository->getByBuildingId($buildingId);
        });
    }

    public function getRoomsWithCctvs()
    {
        // Ultra-fast cache with 0.5 second TTL for maximum responsiveness
        return Cache::remember('rooms_with_cctvs', 0.5, function () {
            return $this->roomRepository->withCctvs();
        });
    }
}
