<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseApiResource;
use App\Http\Resources\RoomResource;
use Illuminate\Support\Collection;

class BuildingResource extends BaseApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Ensure rooms is a collection before using collection method
        $rooms = $this->rooms;
        if (is_array($rooms)) {
            $rooms = collect($rooms);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'marker_icon_url' => $this->marker_icon_url,
            'rooms' => $rooms instanceof Collection ? RoomResource::collection($rooms) : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
