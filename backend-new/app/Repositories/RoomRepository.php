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
        return $this->model->where('building_id', $buildingId)->get();
    }

    public function withCctvs()
    {
        return $this->model->with('cctvs')->get();
    }
}
