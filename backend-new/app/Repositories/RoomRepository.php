<?php

namespace App\Repositories;

use App\Models\Room;
use App\Repositories\BaseRepository;

class RoomRepository extends BaseRepository
{
    public function __construct(Room $model)
    {
        parent::__construct($model);
    }

    public function getByBuildingId(int $buildingId)
    {
        return $this->model
            ->select('id', 'building_id', 'name')
            ->where('building_id', $buildingId)
            ->orderBy('id')
            ->get();
    }

    public function withCctvs()
    {
        return $this->model
            ->select('id', 'building_id', 'name')
            ->with(['cctvs:id,room_id,name,ip_rtsp_url'])
            ->orderBy('id')
            ->get();
    }
}
