<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProceedingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'date' => $this->date,

            'address' => $this->address,

            'location' => $this->location
                ? [
                    'latitude' => (float) DB::selectOne(
                        'SELECT ST_Y(?) AS latitude',
                        [$this->location]
                    )->latitude,

                    'longitude' => (float) DB::selectOne(
                        'SELECT ST_X(?) AS longitude',
                        [$this->location]
                    )->longitude,
                ]
                : null,

            'agenda' => $this->agenda,

            'approaches' => $this->approaches,

            'aggreements' => $this->aggreements,

            'commission_id' => $this->commission_id,

            'commission_name' =>
                $this->commission?->name,


            /**
 * DOCUMENTOS ADJUNTOS
 */
'documents' => $this->whenLoaded(
    'documents',
    function () {

        return $this->documents->map(function ($file) {

            return [
                'id' => $file->id,
                'name' => $file->name,
                'path' => $file->path,
                'size' => Storage::disk('public')->exists($file->path)
                    ? Storage::disk('public')->size($file->path)
                    : null,
                'url' => asset('storage/' . $file->path),
            ];

        });

    },
    []
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



            'createdAt' => 
                $this->created_at?->toIso8601String(),

            'updatedAt' => 
                $this->updated_at?->toIso8601String(),

        ];
    }
}