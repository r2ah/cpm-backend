<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        // Evitamos comportamientos raros en el registrador de validación.
        // Define reglas mínimas para que la validación sea efectiva y no dispare errores
        // por firmas internas.
        return [
            'date' => ['required', 'string'],
            'address' => ['required', 'string'],
            'agenda' => ['nullable', 'string'],


            'approaches' => ['nullable', 'string'],
            'aggreements' => ['nullable', 'string'],
            'commission_id' => ['required', 'integer'],
            'signed_document' => ['nullable', 'integer'],
            'participants' => ['nullable','array'],
            'participants.*' => ['exists:users,id'],
        ];
    }

}
