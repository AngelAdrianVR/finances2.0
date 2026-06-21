<?php

namespace App\Http\Requests\Incomes;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount'              => ['required', 'numeric', 'min:0', 'max:999999'],
            'concept'             => ['required', 'string', 'max:50'],
            'category'            => ['nullable', 'string'],
            'payment_method'      => ['nullable', 'string'],
            'created_at'          => ['required', 'date'],
            'description'         => ['nullable', 'string', 'max:255'],
            'is_recurring_income' => ['nullable', 'boolean'],
            'periodicity'         => ['required_if:is_recurring_income,true', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'              => 'El monto es obligatorio.',
            'amount.numeric'               => 'El monto debe ser un número.',
            'amount.min'                   => 'El monto no puede ser negativo.',
            'amount.max'                   => 'El monto no puede exceder 999,999.',
            'concept.required'             => 'El concepto es obligatorio.',
            'concept.max'                  => 'El concepto no debe exceder 50 caracteres.',
            'created_at.required'          => 'La fecha es obligatoria.',
            'periodicity.required_if'      => 'La periodicidad es obligatoria para ingresos recurrentes.',
        ];
    }
}
