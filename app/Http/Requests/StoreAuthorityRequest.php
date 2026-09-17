<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAuthorityRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:100', Rule::unique(table: 'authorities', column: 'name')->ignore(id: request('authorities'), idColumn: 'id')],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('authorities', 'email'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('Esta Autoridad ya existe.'),
            'email.unique' => __('Ese correo ya se está usando.')
        ];
    }
}
