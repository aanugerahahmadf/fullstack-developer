<?php

namespace App\Repositories;

use App\Models\Building;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class BuildingRepository extends BaseRepository
{
    public function __construct(Building $model)
    {
        parent::__construct($model);
    }

    public function withRoomsAndCctvs(): Collection
    {
        return $this->model
            ->with(['rooms:id,building_id,name', 'rooms.cctvs:id,room_id,name,ip_rtsp_url'])
            ->select('id', 'name', 'latitude', 'longitude', 'marker_icon_url')
            ->get();
    }
}
