<?php

namespace App\Http\Controllers;

use App\Models\BankCard;
use Illuminate\Http\Request;

class BankCardController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'owner_name' => 'nullable|string|max:100',
            'bank_name' => 'required|string|max:100',
            'type' => 'nullable',
            'notes' => 'nullable|string|max:255',
            'is_active' => 'required',
        ]);

        BankCard::create($request->all() + ['user_id' => auth()->id()]);
    }

    public function show(BankCard $bank_card)
    {
        //
    }

    public function edit(BankCard $bank_card)
    {
        //
    }

    public function update(Request $request, BankCard $bank_card)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'owner_name' => 'nullable|string|max:100',
            'bank_name' => 'required|string|max:100',
            'type' => 'nullable',
            'notes' => 'nullable|string|max:255',
            'is_active' => 'required',
        ]);

        $bank_card->update($request->all());
    }

    public function destroy(BankCard $bank_card)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->bankCards as $bank_card) {
            $bank_card = BankCard::find($bank_card['id']);
            $bank_card?->delete();
        }

        return response()->json(['message' => 'Cuenta(s) eliminada(s)']);
    }

    public function toogleStatus(BankCard $bank_card)
    {
        //si la cuenta esta activa, la desactiva y viceversa
        if ( $bank_card->is_active ) {
            $bank_card->update([
                'is_active' => false
            ]);
        } else {
            $bank_card->update([
                'is_active' => true
            ]);
        }

        $bank_card->save();
    }
}
