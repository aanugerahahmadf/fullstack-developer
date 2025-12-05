<?php

namespace App\Http\Controllers\Api;

use App\Services\ProductionTrendService;
use Illuminate\Http\Request;

class ProductionTrendController extends BaseApiController
{
    protected $productionTrendService;

    public function __construct(ProductionTrendService $productionTrendService)
    {
        $this->productionTrendService = $productionTrendService;
    }

    public function index(Request $request)
    {
        try {
            // Get optional date parameters
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            $data = $this->productionTrendService->getProductionTrends($startDate, $endDate);
            // For sample data, return directly without using ChartResource
            return $this->success($this->optimizeData($request, $data), 'Production trends retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve production trends: ' . $e->getMessage(), 500);
        }
    }
}