<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InterventionResource extends JsonResource
{
    public function toArray($request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'parent_id' => $this->parent_id,
    ];
}
}