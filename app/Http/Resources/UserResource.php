<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'users',
            'id' => (string)$this->resource->getRouteKey(),
            'attributes' => [
                'name' => $this->resource->name,
                'email' => $this->resource->email
            ],
            'links' => [
                'self' => route('api.v1.users.show', $this->resource)
            ],
            'relationships' => [
                ProjectResource::collection($this->whenLoaded('projects'))
            ]
        ];
    }

}
