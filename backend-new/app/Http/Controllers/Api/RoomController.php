<?php

namespace App\Http\Controllers\Api;

use App\Services\RoomService;
use App\Http\Resources\RoomResource;
use Illuminate\Http\Request;

class RoomController extends BaseApiController
{
    protected $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function index()
    {
        $rooms = $this->roomService->getRoomsWithCctvs();
        return $this->success(RoomResource::collection($rooms), 'Rooms retrieved successfully');
    }

    public function show($id)
    {
        $room = $this->roomService->getById($id);

        if (!$room) {
            return $this->error('Room not found', 404);
        }

        return $this->success(new RoomResource($room), 'Room retrieved successfully');
    }

    public function getByBuilding($buildingId)
    {
        $rooms = $this->roomService->getRoomsByBuildingId($buildingId);
        return $this->success(RoomResource::collection($rooms), 'Rooms retrieved successfully');
    }
}
