<?php

namespace App\Http\Requests\Outcomes;

use Illuminate\Foundation\Http\FormRequest;

class MassiveDeleteOutcomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:outcomes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Debe seleccionar al menos un gasto para eliminar.',
            'ids.*.exists' => 'Uno de los gastos seleccionados no es válido.',
        ];
    }
}
