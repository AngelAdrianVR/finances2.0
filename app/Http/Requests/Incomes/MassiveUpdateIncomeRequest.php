<?php

namespace App\Http\Requests\Incomes;

use Illuminate\Foundation\Http\FormRequest;

class MassiveUpdateIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selections'     => ['required', 'array', 'min:1'],
            'selections.*.id' => ['required', 'integer', 'exists:incomes,id'],
            'category'       => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
        ];
    }
}
