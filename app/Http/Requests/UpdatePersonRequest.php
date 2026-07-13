<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonRequest extends FormRequest
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
                Rule::unique('people', 'name')->ignore($this->route('person')),
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('people', 'email')->ignore($this->route('person')),
            ],

            'phone' => ['nullable', 'string', 'max:30'],

            'is_natural_person' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('Esta persona ya existe.'),
        ];
    }
}