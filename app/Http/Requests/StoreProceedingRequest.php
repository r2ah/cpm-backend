<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProceedingRequest extends FormRequest
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

            /*
            |--------------------------------------------------------------------------
            | DATOS DEL ACTA
            |--------------------------------------------------------------------------
            */

            'date' => [
                'required',
                'string'
            ],

            'address' => [
                'required',
                'string'
            ],

            'agenda' => [
                'nullable',
                'string'
            ],

            'approaches' => [
                'nullable',
                'string'
            ],

            'aggreements' => [
                'nullable',
                'string'
            ],


            /*
            |--------------------------------------------------------------------------
            | ELABORADO POR
            |--------------------------------------------------------------------------
            */

            'elaborado_por' => [
                'required',
                'integer',
                'exists:users,id'
            ],


            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO FIRMADO
            |--------------------------------------------------------------------------
            */

            'signed_document' => [
                'nullable',
                'integer',
                'exists:media_files,id'
            ],


            /*
            |--------------------------------------------------------------------------
            | DOCUMENTOS ASOCIADOS
            |--------------------------------------------------------------------------
            */

            'documents' => [
                'nullable',
                'array'
            ],

            'documents.*' => [
                'integer',
                'exists:media_files,id'
            ],


            /*
            |--------------------------------------------------------------------------
            | PARTICIPANTES
            |--------------------------------------------------------------------------
            */

            'participants' => [
                'nullable',
                'array'
            ],

            'participants.*' => [
                'integer',
                'exists:users,id'
            ],

            /**
 * LOCALIZACIÓN
 */
'location' => [
    'nullable',
    'array'
],

'location.latitude' => [
    'required_with:location',
    'numeric',
    'between:-90,90'
],

'location.longitude' => [
    'required_with:location',
    'numeric',
    'between:-180,180'
],
        ];

        
    }

    
}