<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    public function toArray($request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'phone' => $this->phone,

        // ✔ CAMPO REAL
        'is_natural_person' => (bool) $this->is_natural_person,

        'createdAt' => $this->created_at?->toIso8601String(),
        'updatedAt' => $this->updated_at?->toIso8601String(),
    ];
}
}