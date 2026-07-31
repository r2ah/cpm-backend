<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class OpinionResource extends JsonResource
{

public function toArray(Request $request): array
{
    return [

        'id'=>$this->id,

        'entity'=>$this->entity,

        'address'=>$this->address,

        'designer'=>$this->designer,

        'investor'=>$this->investor,

        'builder'=>$this->builder,

        'general_characteristics'=>$this->general_characteristics,

        'issuing_company'=>$this->issuingCompany,

        'issuing_document_code'=>$this->issuing_document_code,

        'considerations'=>$this->considerations,

        'observations'=>$this->observations,

        'state'=>$this->state,

        'date'=>$this->date,


        'commission'=>$this->commission,

        'prepared_by'=>$this->preparedBy,

        'reviewed_by'=>$this->reviewedBy,

        'approved_by'=>$this->approvedBy,
        
        'interventions' =>$this->interventions,

        'documents' => $this->whenLoaded('documents', function(){

    return $this->documents->map(function($document){

        return [

            'id'=>$document->id,

            'name'=>$document->original_name,

            'download_url' =>
                url(
                    "/api/v1/opinion-documents/{$document->id}/download"
                ),

            'mime_type'=>$document->mime_type,

            'size'=>$document->size

        ];

    });

}),


        'createdAt'=>$this->created_at?->toIso8601String(),

        'updatedAt'=>$this->updated_at?->toIso8601String(),

    ];
}

}