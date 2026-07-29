<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOpinionRequest extends FormRequest
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

        'entity'=>[
            'required',
            'integer'
        ],

        'address'=>[
            'required',
            'string'
        ],


        'designer_id'=>[
            'required',
            'integer',
            'exists:people,id'
        ],


        'investor_id'=>[
            'required',
            'integer',
            'exists:people,id'
        ],


        'builder_id'=>[
            'required',
            'integer',
            'exists:people,id'
        ],


        'general_characteristics'=>[
            'nullable',
            'string'
        ],


        'issuing_company'=>[
            'required',
            'integer',
            'exists:authorities,id'
        ],


        'issuing_document_code'=>[
            'required',
            'in:DUS,DO,Micro'
        ],


        'considerations'=>[
            'nullable',
            'string'
        ],


        'observations'=>[
            'nullable',
            'string'
        ],


        'state'=>[
            'nullable',
            'in:Elaborado,Revisado,Aprobado,Denegado'
        ],


        'date'=>[
            'required',
            'date'
        ],


        'commission_id' => [
            'nullable',
            'exists:commissions,id'
        ],


        'prepared_by'=>[
            'required',
            'exists:users,id'
        ],


        'reviewed_by'=>[
            'required',
            'exists:users,id'
        ],


        'approved_by'=>[
            'required',
            'exists:users,id'
        ],

        'intervention_ids'=>[
        'nullable',
        'array'
        ],


        'intervention_ids.*'=>[
        'integer',
        'exists:interventions,id'
        ],

    ];
}
}
