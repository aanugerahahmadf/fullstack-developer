<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseApiResource;

class ChartResource extends BaseApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Handle both array and object cases
        if (is_array($this->resource)) {
            // If resource is an array, return its contents directly
            return $this->resource;
        } else {
            // If resource is an object, access its properties
            return [
                'id' => $this->id,
                'title' => $this->title,
                'data' => $this->data,
                'type' => $this->type,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }
    }
}
