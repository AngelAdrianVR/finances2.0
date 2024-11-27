<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        // $reminders = Calendar::where('user_id', auth()->id())->get();

        return inertia('Calendar/Index');
    }

    public function create(Request $request)
    {   
        $type = $request->type;

        return inertia('Calendar/Create', compact('type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'title' => 'required|string|max:80',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0|max:999999',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'periodicity' => 'required|string',
            'payment_method' => 'nullable',
        ]);

        Calendar::create($request->all() + ['user_id' => auth()->id()]);

        return to_route('calendars.index');
    }

    public function show(Calendar $calendar)
    {
        //
    }

    public function edit(Calendar $calendar)
    {
        //
    }

    public function update(Request $request, Calendar $calendar)
    {
        //
    }

    public function destroy(Calendar $calendar)
    {
        //
    }

    public function fetchMonthReminders(Request $request)
    {
        // Validar que se reciban los datos necesarios
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        // Obtener los recordatorios del mes y año proporcionados
        $reminders = Calendar::whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->get();

        // Retornar los recordatorios como JSON
        return response()->json(['reminders' => $reminders]);
    }
}
