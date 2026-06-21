<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount'         => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:255'],
            'date'           => ['required', 'date'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'loan_id'        => ['required', 'integer', 'exists:loans,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'         => 'El monto del abono es obligatorio.',
            'amount.min'              => 'El abono debe ser al menos 1.',
            'payment_method.required' => 'El método de pago es obligatorio.',
            'date.required'           => 'La fecha del abono es obligatoria.',
            'loan_id.required'        => 'El préstamo asociado es obligatorio.',
            'loan_id.exists'          => 'El préstamo seleccionado no existe.',
        ];
    }
}
