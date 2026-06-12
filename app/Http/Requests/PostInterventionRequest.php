<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostInterventionRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'min:3', 'max:100', Rule::unique(table: 'authorities', column: 'name')->ignore(id: request('authorities'), idColumn: 'id')]
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('Este tipo de intervención ya existe.')
        ];
    }    
}
