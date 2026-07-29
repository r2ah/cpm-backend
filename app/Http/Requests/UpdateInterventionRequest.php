<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInterventionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {

        $interventionId = $this->route('intervention')->id;


        return [

            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:100',

                Rule::unique('interventions','name')
                    ->ignore($interventionId)
            ],


            'parent_id' => [

                'nullable',

                'exists:interventions,id',

                // No puede ser él mismo
                function ($attribute, $value, $fail) use ($interventionId) {

                    if ($value == $interventionId) {

                        $fail(
                            'Una intervención no puede ser superior de sí misma.'
                        );

                    }

                },

            ]

        ];

    }


    public function messages(): array
    {
        return [

            'name.unique' =>
            'Este tipo de intervención ya existe.',


            'parent_id.exists' =>
            'La intervención superior seleccionada no existe.'

        ];
    }

}