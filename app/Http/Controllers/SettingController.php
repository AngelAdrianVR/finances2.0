<?php

namespace App\Http\Controllers;

use App\Models\BankCard;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {   
        $bankCards = BankCard::where('user_id', auth()->id())->paginate(20);

        return inertia('Setting/Index', compact('bankCards'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Setting $setting)
    {
        //
    }

    public function edit(Setting $setting)
    {
        //
    }

    public function update(Request $request, Setting $setting)
    {
        //
    }

    public function destroy(Setting $setting)
    {
        //
    }
}
