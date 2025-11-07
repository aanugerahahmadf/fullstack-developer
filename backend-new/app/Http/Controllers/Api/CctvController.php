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
        $streamData = $this->cctvService->getStreamUrl($id);

        if (!$streamData) {
            return $this->error('CCTV not found', 404);
        }

        return $this->success($streamData, 'Stream URL retrieved successfully');
    }
}
