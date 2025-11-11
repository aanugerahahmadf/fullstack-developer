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
        // Ultra-fast cache with 0.5 second TTL for maximum responsiveness
        return Cache::remember("cctvs_by_room_{$roomId}", 0.5, function () use ($roomId) {
            return $this->cctvRepository->getByRoomId($roomId);
        });
    }

    public function getStreamUrl(int $id)
    {
        // Ultra-fast cache with 0.5 second TTL for maximum responsiveness
        return Cache::remember("cctv_stream_url_{$id}", 0.5, function () use ($id) {
            return $this->cctvRepository->getStreamUrl($id);
        });
    }
}
