<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProceedingRequest extends FormRequest
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


        'elaborado_por' => [
            'required',
            'integer',
            'exists:users,id'
        ],


        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS
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
