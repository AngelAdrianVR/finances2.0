<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvailableMoneyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_money' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'total_money.required' => 'El monto disponible es obligatorio.',
            'total_money.numeric'  => 'El monto disponible debe ser un número.',
            'total_money.min'      => 'El monto disponible no puede ser negativo.',
        ];
    }
}
