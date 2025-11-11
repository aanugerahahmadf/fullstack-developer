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

    public function productionTrends(Request $request)
    {
        try {
            // Get optional date parameters
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            $data = $this->chartService->getProductionTrends($startDate, $endDate);
            // For sample data, return directly without using ChartResource
            return $this->success($this->optimizeData($request, $data), 'Production trends retrieved successfully');
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
            return $this->success($this->optimizeData(request(), $data), 'Unit performance data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve unit performance data: ' . $e->getMessage(), 500);
        }
    }
}
