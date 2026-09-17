<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAuthorityRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:100',
                Rule::unique('authorities', 'name')
                    ->ignore($this->route('authority')->id),
            ],
            'email' => [
                'sometimes',
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('authorities', 'email')
                    ->ignore($this->route('authority')->id),
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
