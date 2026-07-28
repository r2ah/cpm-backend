<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [

            'id'=>$this->id,

            'name'=>$this->name,

            'email'=>$this->email,

            'level'=>$this->level,


            'parent'=>[
                'id'=>$this->parent?->id,
                'name'=>$this->parent?->name,
            ],


            'members'=>$this->whenLoaded('members', function(){

                return $this->members->map(function($member){

                    return [
                        'id'=>$member->id,
                        'name'=>$member->name,
                        'email'=>$member->email,
                        'position'=>$member->pivot->position ?? null,
                    ];

                });

            }),


            'createdAt'=>$this->created_at?->toIso8601String(),

            'updatedAt'=>$this->updated_at?->toIso8601String(),

        ];
    }
}
