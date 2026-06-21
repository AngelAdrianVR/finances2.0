<?php

namespace App\Http\Requests\Loans;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'                => ['required', 'string', 'in:Otorgado,Recibido'],
            'beneficiary_name'    => ['required_if:type,Otorgado', 'nullable', 'string', 'max:100'],
            'lender_name'         => ['required_if:type,Recibido', 'nullable', 'string', 'max:100'],
            'payment_periodicity' => ['nullable', 'string'],
            'profitability'       => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'profitability_type'  => ['nullable', 'string'],
            'profitability_mode'  => ['nullable', 'string', 'in:Porcentaje,Monto fijo'],
            'profitability_period' => ['nullable', 'string'],
            'expired_date'        => ['nullable', 'date', 'after:today'],
            'amount'              => ['required', 'numeric', 'min:0', 'max:999999'],
            'status'              => ['required', 'string'],
            'loan_date'           => ['nullable', 'date'],
            'automatic'           => ['nullable', 'boolean'],
            'no_interest'         => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // If no_interest is checked, set profitability to null
        if ($this->boolean('no_interest')) {
            $this->merge([
                'profitability' => null,
                'profitability_type' => null,
                'profitability_mode' => null,
                'profitability_period' => null,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'amount.required'          => 'El monto es obligatorio.',
            'amount.numeric'           => 'El monto debe ser un número.',
            'amount.min'               => 'El monto no puede ser negativo.',
            'amount.max'               => 'El monto no puede exceder 999,999.',
            'beneficiary_name.required_if' => 'El nombre del beneficiario es obligatorio para préstamos otorgados.',
            'lender_name.required_if'      => 'El nombre del prestamista es obligatorio para préstamos recibidos.',
            'expired_date.after'       => 'La fecha de vencimiento debe ser posterior a hoy.',
        ];
    }
}
