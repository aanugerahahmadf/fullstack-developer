<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseApiResource;
use App\Http\Resources\CctvResource;
use Illuminate\Support\Collection;

class RoomResource extends BaseApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Ensure cctvs is a collection before using collection method
        $cctvs = $this->cctvs;
        if (is_array($cctvs)) {
            $cctvs = collect($cctvs);
        }

        return [
            'id' => $this->id,
            'building_id' => $this->building_id,
            'name' => $this->name,
            'cctvs' => $cctvs instanceof Collection ? CctvResource::collection($cctvs) : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
