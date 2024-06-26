<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'route' => $this->route,
            'icon' => $this->icon,
            'category' => $this->category,
            'category_name' => $this->category_name,
            'guard_name' => $this->guard_name,
            'children' => PermissionResource::collection($this->whenLoaded('children')),
        ];
    }
}
