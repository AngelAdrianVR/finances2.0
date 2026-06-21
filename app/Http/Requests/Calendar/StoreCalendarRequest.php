<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'string', 'in:Ingreso recurrente,Gasto fijo'],
            'title'          => ['required', 'string', 'max:80'],
            'date'           => ['required', 'date'],
            'amount'         => ['required', 'numeric', 'min:0', 'max:999999'],
            'category'       => ['nullable', 'string', 'max:50'],
            'description'    => ['nullable', 'string', 'max:255'],
            'periodicity'    => ['required', 'string'],
            'payment_method' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'      => 'El título es obligatorio.',
            'title.max'           => 'El título no debe exceder 80 caracteres.',
            'amount.required'     => 'El monto es obligatorio.',
            'amount.numeric'      => 'El monto debe ser un número.',
            'amount.min'          => 'El monto no puede ser negativo.',
            'amount.max'          => 'El monto no puede exceder 999,999.',
            'date.required'       => 'La fecha es obligatoria.',
            'periodicity.required'=> 'La periodicidad es obligatoria.',
        ];
    }
}
