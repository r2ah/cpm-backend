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


'designer_id'=>$this->designer_id,

'investor_id'=>$this->investor_id,

'builder_id'=>$this->builder_id,


'general_characteristics'=>$this->general_characteristics,


'issuing_company'=>$this->issuing_company,

'issuing_document_code'=>$this->issuing_document_code,


'considerations'=>$this->considerations,

'observations'=>$this->observations,


'state'=>$this->state,


'date'=>$this->date,


'commission_id'=>$this->commission_id,


'prepared_by'=>$this->prepared_by,

'reviewed_by'=>$this->reviewed_by,

'approved_by'=>$this->approved_by,


'designer'=>$this->designer,

'investor'=>$this->investor,

'builder'=>$this->builder,


'createdAt'=>$this->created_at?->toIso8601String(),

'updatedAt'=>$this->updated_at?->toIso8601String(),


];

}

}