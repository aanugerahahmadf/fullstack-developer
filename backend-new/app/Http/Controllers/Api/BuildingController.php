<?php

namespace App\Http\Controllers\Api;

use App\Services\BuildingService;
use App\Http\Resources\BuildingResource;
use Illuminate\Http\Request;

class BuildingController extends BaseApiController
{
    protected $buildingService;

    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    public function index()
    {
        try {
            // Use the existing method that fetches buildings with rooms and CCTVs
            $buildings = $this->buildingService->getBuildingsWithRoomsAndCctvs();

            // Return optimized data
            return $this->success($this->optimizeData(request(), $buildings), 'Buildings retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve buildings: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            // Use the base service method to find a building
            $building = $this->buildingService->getById($id);

            if (!$building) {
                return $this->notFound('Building not found');
            }

            return $this->success(new BuildingResource($building), 'Building retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve building: ' . $e->getMessage(), 500);
        }
    }
}
