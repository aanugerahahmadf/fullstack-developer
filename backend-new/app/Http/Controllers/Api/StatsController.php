<?php

namespace App\Http\Controllers\Api;

use App\Services\StatsService;
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
        try {
            $stats = $this->statsService->getStats();
            // For maximum performance, return directly without using StatsResource
            return $this->success($stats, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve statistics: ' . $e->getMessage(), 500);
        }
    }
}
