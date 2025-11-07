<?php

namespace App\Http\Controllers\Api;

use App\Services\StatsService;
use App\Http\Resources\StatsResource;
use Illuminate\Http\Request;

class StatsController extends BaseApiController
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index()
    {
        $stats = $this->statsService->getStats();
        // For sample data, return directly without using StatsResource
        return $this->success($stats, 'Statistics retrieved successfully');
    }
}
