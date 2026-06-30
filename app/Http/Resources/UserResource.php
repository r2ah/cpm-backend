<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
{
    return [
        'id'    => $this->id,
        'name'  => $this->name,
        'email' => $this->email,
        'phone' => $this->phone,

        // 🔥 SPATIE ROLES
        'roles' => $this->getRoleNames(),

        'createdAt' => $this->created_at?->toIso8601String(),
        'updatedAt' => $this->updated_at?->toIso8601String(),
    ];
}
}