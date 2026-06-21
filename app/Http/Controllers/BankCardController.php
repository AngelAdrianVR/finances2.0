<?php

namespace App\Http\Controllers;

use App\Models\BankCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BankCardController extends Controller
{
    // ========================
    // CRUD
    // ========================

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'owner_name' => ['nullable', 'string', 'max:100'],
            'bank_name'  => ['required', 'string', 'max:100'],
            'type'       => ['nullable', 'string'],
            'notes'      => ['nullable', 'string', 'max:255'],
            'is_active'  => ['required', 'boolean'],
        ]);

        BankCard::create($validated + ['user_id' => auth()->id()]);

        return to_route('settings.index');
    }

    public function update(Request $request, BankCard $bank_card): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'owner_name' => ['nullable', 'string', 'max:100'],
            'bank_name'  => ['required', 'string', 'max:100'],
            'type'       => ['nullable', 'string'],
            'notes'      => ['nullable', 'string', 'max:255'],
            'is_active'  => ['required', 'boolean'],
        ]);

        $bank_card->update($validated);

        return to_route('settings.index');
    }

    public function destroy(BankCard $bank_card): RedirectResponse
    {
        $bank_card->delete();

        return to_route('settings.index');
    }

    // ========================
    // Massive & toggle
    // ========================

    public function massiveDelete(Request $request): JsonResponse
    {
        $ids = array_column($request->input('bankCards', []), 'id');
        BankCard::forUser()->whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Cuenta(s) eliminada(s)']);
    }

    public function toogleStatus(BankCard $bank_card): JsonResponse
    {
        $bank_card->toggle();

        return response()->json(['is_active' => $bank_card->is_active]);
    }
}
