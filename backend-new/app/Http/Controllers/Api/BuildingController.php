<?php

namespace App\Http\Controllers\Api;

use App\Services\BuildingService;
use App\Http\Resources\BuildingResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class BuildingController extends BaseApiController
{
    protected $buildingService;

    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    public function index()
    {
        $buildings = $this->buildingService->getBuildingsWithRoomsAndCctvs();
        return $this->success(BuildingResource::collection($buildings), 'Buildings retrieved successfully');
    }

    public function show($id)
    {
        $building = $this->buildingService->getById($id);

        if (!$building) {
            return $this->error('Building not found', 404);
        }

        return $this->success(new BuildingResource($building), 'Building retrieved successfully');
    }

    // Add methods for create/update/delete that clear cache
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'marker_icon_url' => 'nullable|string|max:255',
        ]);

        // Create building
        $building = $this->buildingService->create($validated);

        // Clear cache
        $this->buildingService->clearCache();

        return $this->success(new BuildingResource($building), 'Building created successfully', 201);
    }

    public function update(Request $request, $id)
    {
        // Validate request
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'marker_icon_url' => 'nullable|string|max:255',
        ]);

        // Update building
        $building = $this->buildingService->update($id, $validated);

        if (!$building) {
            return $this->error('Building not found', 404);
        }

        // Clear cache
        $this->buildingService->clearCache();

        return $this->success(new BuildingResource($building), 'Building updated successfully');
    }

    public function destroy($id)
    {
        // Delete building
        $deleted = $this->buildingService->delete($id);

        if (!$deleted) {
            return $this->error('Building not found', 404);
        }

        // Clear cache
        $this->buildingService->clearCache();

        return $this->success(null, 'Building deleted successfully');
    }
}
