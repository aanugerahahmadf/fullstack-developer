<?php

namespace App\Http\Controllers\Api;

use App\Services\ChartService;
use App\Http\Resources\ChartResource;
use Illuminate\Http\Request;

class ChartController extends BaseApiController
{
    protected $chartService;

    public function __construct(ChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    public function productionTrends()
    {
        $data = $this->chartService->getProductionTrends();
        // For sample data, return directly without using ChartResource
        return $this->success($data, 'Production trends retrieved successfully');
    }

    public function unitPerformance()
    {
        $data = $this->chartService->getUnitPerformance();
        // For sample data, return directly without using ChartResource
        // Ensure fast JSON response
        return response()->json([
            'success' => true,
            'message' => 'Unit performance data retrieved successfully',
            'data' => $data
        ], 200);
    }
}
