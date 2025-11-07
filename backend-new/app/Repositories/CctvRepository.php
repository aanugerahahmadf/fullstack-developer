<?php

namespace App\Repositories;

use App\Models\Cctv;
use App\Repositories\BaseRepository;

class CctvRepository extends BaseRepository
{
    public function __construct(Cctv $model)
    {
        parent::__construct($model);
    }

    public function getByRoomId(int $roomId)
    {
        return $this->model->where('room_id', $roomId)->get();
    }

    public function getStreamUrl(int $id)
    {
        $cctv = $this->find($id);
        if ($cctv) {
            return [
                'stream_url' => $cctv->ip_rtsp_url
            ];
        }
        return null;
    }
}
