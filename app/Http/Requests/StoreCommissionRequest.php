<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'level' => [
                'required',
                'string',
                'max:100'
            ],

            'parent_id' => [
                'nullable',
                'exists:commissions,id'
            ],

        ];
    }


    public function messages(): array
    {
        return [

            'name.required' =>
                'El nombre de la comisión es obligatorio.',

            'level.required' =>
                'El nivel de la comisión es obligatorio.',

            'parent_id.exists' =>
                'La comisión padre seleccionada no existe.',

        ];
    }
}