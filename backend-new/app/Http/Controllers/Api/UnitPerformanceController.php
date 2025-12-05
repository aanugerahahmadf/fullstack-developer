<?php

namespace App\Http\Controllers\Api;

use App\Services\UnitPerformanceService;
use Illuminate\Http\Request;

class UnitPerformanceController extends BaseApiController
{
    protected $unitPerformanceService;

    public function __construct(UnitPerformanceService $unitPerformanceService)
    {
        $this->unitPerformanceService = $unitPerformanceService;
    }

    public function index()
    {
        try {
            $data = $this->unitPerformanceService->getUnitPerformance();
            // For sample data, return directly without using ChartResource
            // Ensure fast JSON response
            return $this->success($this->optimizeData(request(), $data), 'Unit performance data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve unit performance data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get detailed ATCS metrics for a specific unit
     *
     * @param string $unitName
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($unitName)
    {
        try {
            $data = $this->unitPerformanceService->getUnitPerformance();
            $unitData = collect($data)->firstWhere('unit', $unitName);
            
            if (!$unitData) {
                return $this->error('Unit not found', 404);
            }
            
            return $this->success($unitData, 'Unit performance data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve unit performance data: ' . $e->getMessage(), 500);
        }
    }
}