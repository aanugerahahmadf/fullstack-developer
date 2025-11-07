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
        try {
            $data = $this->chartService->getProductionTrends();
            // For sample data, return directly without using ChartResource
            return $this->success($this->optimizeData(request(), $data), 'Production trends retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve production trends: ' . $e->getMessage(), 500);
        }
    }

    public function unitPerformance()
    {
        try {
            $data = $this->chartService->getUnitPerformance();
            // For sample data, return directly without using ChartResource
            // Ensure fast JSON response
            return response()->json([
                'success' => true,
                'message' => 'Unit performance data retrieved successfully',
                'data' => $this->optimizeData(request(), $data)
            ], 200);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve unit performance data: ' . $e->getMessage(), 500);
        }
    }
}
