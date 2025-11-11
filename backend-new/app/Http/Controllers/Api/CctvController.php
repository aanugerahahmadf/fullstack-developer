<?php

namespace App\Http\Controllers\Api;

use App\Services\CctvService;
use App\Http\Resources\CctvResource;
use Illuminate\Http\Request;

class CctvController extends BaseApiController
{
    protected $cctvService;

    public function __construct(CctvService $cctvService)
    {
        $this->cctvService = $cctvService;
    }

    public function index()
    {
        $cctvs = $this->cctvService->getAll();
        return $this->success(CctvResource::collection($cctvs), 'CCTVs retrieved successfully');
    }

    public function show($id)
    {
        $cctv = $this->cctvService->getById($id);

        if (!$cctv) {
            return $this->error('CCTV not found', 404);
        }

        return $this->success(new CctvResource($cctv), 'CCTV retrieved successfully');
    }

    public function getByRoom($roomId)
    {
        $cctvs = $this->cctvService->getCctvsByRoomId($roomId);
        return $this->success(CctvResource::collection($cctvs), 'CCTVs retrieved successfully');
    }

    public function getStreamUrl($id)
    {
        try {
            $streamData = $this->cctvService->getStreamUrl((int) $id);

            if (!$streamData) {
                return $this->error('CCTV not found', 404);
            }

            // Guarantee required keys and types for frontend
            $safe = [
                'stream_url' => (string)($streamData['stream_url'] ?? ''),
                'rtsp_url'   => (string)($streamData['rtsp_url'] ?? ''),
            ];

            return $this->success($safe, 'Stream URL retrieved successfully');
        } catch (\Throwable $e) {
            // Never expose stack traces to client; provide safe fallback
            return $this->error('Failed to retrieve stream URL', 500);
        }
    }
}
