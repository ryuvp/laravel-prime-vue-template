<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'code' => $this->code,
            'email' => $this->email,
            'status' => $this->status,
            'status_format' => $this->status_format,
            'sex' => $this->sex,
            'sex_format' => $this->sex_format,
            'age' => $this->age,
            'birthdate' => $this->birthdate,
            'roles' => array_map(
                function ($role) {
                    return $role['name'];
                },
                $this->roles->toArray()
            ),
            'permissions' => array_map(
                function ($permission) {
                    return [
                        'name' => $permission['name'],
                        'description' => $permission['description'],
                        'route' => $permission['route'],
                        'icon' => $permission['icon'],
                    ];
                },
                $this->getAllPermissions()->toArray()
            ),
        ];
    }
}
