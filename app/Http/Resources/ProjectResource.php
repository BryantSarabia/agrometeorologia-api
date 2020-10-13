<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'projects',
            'id' => (string)$this->resource->getRouteKey(),
            'attributes' => [
                'name' => $this->resource->name,
            ],
            'links' => [
                'self' => route('api.v1.projects.show',$this->getRouteKey())
            ],
            'relationships' => [
                'users' => ['data' => UserResource::make($this->whenLoaded('user'))],
                'requests' => RequestCollection::make($this->whenLoaded('requests'))
            ]
        ];
    }
}
