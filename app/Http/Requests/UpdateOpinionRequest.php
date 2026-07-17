<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOpinionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [

        'entity'=>'sometimes|integer',

        'address'=>'sometimes|string',

        'designer_id'=>'sometimes|exists:people,id',

        'investor_id'=>'sometimes|exists:people,id',

        'builder_id'=>'sometimes|exists:people,id',

        'general_characteristics'=>'nullable|string',

        'issuing_company'=>'sometimes|exists:authorities,id',

        'issuing_document_code'=>'sometimes|in:DUS,DO,Micro',

        'considerations'=>'nullable|string',

        'observations'=>'nullable|string',

        'state'=>'sometimes|in:Elaborado,Revisado,Aprobado,Denegado',

        'date'=>'sometimes|date',

        'commission_id'=>'sometimes|exists:commissions,id',

        'prepared_by'=>'sometimes|exists:users,id',

        'reviewed_by'=>'sometimes|exists:users,id',

        'approved_by'=>'sometimes|exists:users,id',

    ];
}
}
