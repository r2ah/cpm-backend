<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInterventionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                Rule::unique('interventions', 'name')
            ],

            'parent_id' => [
                'nullable',
                'exists:interventions,id'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Este tipo de intervención ya existe.',
        ];
    }
}