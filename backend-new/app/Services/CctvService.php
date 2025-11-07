<?php

namespace App\Services;

use App\Repositories\CctvRepository;
use Illuminate\Support\Facades\Cache;

class CctvService extends BaseService
{
    protected $cctvRepository;

    public function __construct(CctvRepository $cctvRepository)
    {
        parent::__construct($cctvRepository);
        $this->cctvRepository = $cctvRepository;
    }

    public function getCctvsByRoomId(int $roomId)
    {
        // Cache the result for 30 seconds to reduce database load and improve response time
        return Cache::remember("cctvs_by_room_{$roomId}", 30, function () use ($roomId) {
            return $this->cctvRepository->getByRoomId($roomId);
        });
    }

    public function getStreamUrl(int $id)
    {
        // Cache the result for 30 seconds to reduce database load and improve response time
        return Cache::remember("cctv_stream_url_{$id}", 30, function () use ($id) {
            return $this->cctvRepository->getStreamUrl($id);
        });
    }
}
