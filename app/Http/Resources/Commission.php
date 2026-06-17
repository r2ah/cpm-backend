<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'level'     => $this->level,
            'region'    => $this->region,
            'parent'    => [
                'id'    => $this->parent?->id,
                'name'  => $this->parent?->name,
            ],
            'members'   => $this->members,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}