<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProceedingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'date' => $this->date,
            'address' => $this->address,
            'agenda' => $this->agenda,
            'approaches' => $this->approaches,
            'aggreements' => $this->aggreements,

            'commission_id' => $this->commission_id,
            'commission_name' => $this->commission?->name,
            'signed_document' => $this->signed_document,
            'document' => $this->whenLoaded(
    'signedDocument',
    function () {

        return [
            'id' => $this->signedDocument->id,
            'name' => basename($this->signedDocument->path),
        ];

    }
),


            'elaborado_por' => $this->elaborado_por,

'elaborador' => $this->whenLoaded(
    'elaboradoPor',
    function () {
        return [
            'id' => $this->elaboradoPor->id,
            'name' => $this->elaboradoPor->name,
        ];
    }
),

            'participants' => $this->whenLoaded(
                'participants',
                function () {
                    return $this->participants->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ];
                    });
                }
            ),

            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}