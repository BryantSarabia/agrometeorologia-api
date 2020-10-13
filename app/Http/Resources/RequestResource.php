<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'type' => 'requests',
            'id' => (string) $this->resource->getRouteKey(),
            'attributes' => [
                'endpoint' => $this->resource->endpoint,
                'number' => $this->resource->number,
                'date' => $this->resource->date,
            ],
            'links' => [
                'self' => route('api.v1.requests.show', $this->resource->getRouteKey()),
            ],
            'relationships' => [
                'projects' => ['data' => ProjectResource::make($this->whenLoaded('project'))],
            ]

        ];
    }
}
