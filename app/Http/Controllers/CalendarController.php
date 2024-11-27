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
        return inertia('Calendar/Edit', compact('calendar'));
    }

    public function update(Request $request, Calendar $calendar)
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

        $calendar->update($request->all());

        return to_route('calendars.index');
    }

    public function destroy(Request $request, Calendar $calendar)
    {
        $deleteType = $request->query('deleteOption');

        // return $deleteType;
        if ( $deleteType === "Este" ) {
            //eliminar solo ese recordatorio
            $calendar->delete();

        } else if ( $deleteType === "Este y los siguientes" ) {
            //eliminar los recordatorios con el mismo nombre desde este y fechas posteriores
            Calendar::where('title', $calendar->title)->where('date', '>=', $calendar->date)->delete();
            
        } else if ( $deleteType === "Todos" ) {
            //eliminar todos los recordatorios con el mismo nombre
            Calendar::where('title', $calendar->title)->delete();
        }
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
