<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\RecurringIncome;
use App\Models\RecurringOutcome;
use Carbon\Carbon;
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

        if ( $request->type === 'Ingreso recurrente' ) {
            //agrega el ingreso recurrente a su tabla
            RecurringIncome::create([
                'concept' => $request->title,
                'periodicity' => $request->periodicity,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
                'category' => $request->category,
                'created_at' => $request->date,
                'user_id' => auth()->id(),
            ]);
        } else {
            //agrega el gasto fijo a su tabla
            RecurringOutcome::create([
                'concept' => $request->title,
                'periodicity' => $request->periodicity,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
                'category' => $request->category,
                'created_at' => $request->date,
                'user_id' => auth()->id(),
            ]);
        }
        
        //agregar a calendario el ingreso recurrente con la frecuencia indicada ---------------------
        $startDate = Carbon::parse($request->date);
        $endDate = Carbon::now()->endOfYear(); // Limitar a este año
        $dates = [];

        switch ($request->periodicity) {
            case 'Todos los días':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addDay();
                }
                break;

            case 'Semanal':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addWeek();
                }
                break;

            case 'Mensual':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addMonth();
                }
                break;

                case 'Anual':
                    $endDate = Carbon::parse($request->date)->addYears(3); // 3 años posteriores
                    while ($startDate->lte($endDate)) {
                        $dates[] = $startDate->copy();
                        $startDate->addYear();
                    }
                    break;
        }

        foreach ($dates as $date) {
            Calendar::create([
                'type' => $request->type,
                'title' => $request->title,
                'date' => $date->toDateString(),
                'amount' => $request->amount,
                'category' => $request->category,
                'description' => $request->description,
                'periodicity' => $request->periodicity,
                'payment_method' => $request->payment_method,
                'user_id' => auth()->id(),
            ]);
        }

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

        if ( $request->type === 'Ingreso recurrente' ) {
            //elimina el ingreso recurrente de la tabla de recurring incomes
            RecurringIncome::where('concept', $calendar->title)->delete();

            //agrega el ingreso recurrente a su tabla
            RecurringIncome::create([
                'concept' => $request->title,
                'periodicity' => $request->periodicity,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
                'category' => $request->category,
                'created_at' => $request->date,
                'user_id' => auth()->id(),
            ]);
        } else {
            //elimina el ingreso recurrente de la tabla de recurring incomes
            RecurringOutcome::where('concept', $calendar->title)->delete();

            //agrega el gasto fijo a su tabla
            RecurringOutcome::create([
                'concept' => $request->title,
                'periodicity' => $request->periodicity,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
                'category' => $request->category,
                'created_at' => $request->date,
                'user_id' => auth()->id(),
            ]);
        }

        //eliminar todos los registros del calendario con el nombre del recordatorio editado.
        Calendar::where('title', $calendar->title)->delete();

        //agregar a calendario el ingreso recurrente con la frecuencia indicada ---------------------
        $startDate = Carbon::parse($request->date);
        $endDate = Carbon::now()->endOfYear(); // Limitar a este año
        $dates = [];

        switch ($request->periodicity) {
            case 'Todos los días':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addDay();
                }
                break;

            case 'Semanal':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addWeek();
                }
                break;

            case 'Mensual':
                while ($startDate->lte($endDate)) {
                    $dates[] = $startDate->copy();
                    $startDate->addMonth();
                }
                break;

                case 'Anual':
                    $endDate = Carbon::parse($request->date)->addYears(3); // 3 años posteriores
                    while ($startDate->lte($endDate)) {
                        $dates[] = $startDate->copy();
                        $startDate->addYear();
                    }
                    break;
        }

        foreach ($dates as $date) {
            Calendar::create([
                'type' => $request->type,
                'title' => $request->title,
                'date' => $date->toDateString(),
                'amount' => $request->amount,
                'category' => $request->category,
                'description' => $request->description,
                'periodicity' => $request->periodicity,
                'payment_method' => $request->payment_method,
                'user_id' => auth()->id(),
            ]);
        }

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
            //elimina el ingreso recurrente de la tabla de recurring incomes
            RecurringIncome::where('concept', $calendar->title)->delete();
            //elimina el gasto fijo de la tabla de recurring outcomes
            RecurringOutcome::where('concept', $calendar->title)->delete();
            
        } else if ( $deleteType === "Todos" ) {
            //eliminar todos los recordatorios con el mismo nombre
            Calendar::where('title', $calendar->title)->delete();
            //elimina el ingreso recurrente de la tabla de recurring incomes
            RecurringIncome::where('concept', $calendar->title)->delete();
            //elimina el gasto fijo de la tabla de recurring outcomes
            RecurringOutcome::where('concept', $calendar->title)->delete();
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
            ->where('user_id', auth()->id())
            ->get();

        // Retornar los recordatorios como JSON
        return response()->json(['reminders' => $reminders]);
    }
}
