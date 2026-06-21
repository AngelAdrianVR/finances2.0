<?php

namespace App\Http\Controllers;

use App\Actions\Calendar\CreateCalendarEventAction;
use App\Actions\Calendar\DeleteCalendarEventAction;
use App\Actions\Calendar\UpdateCalendarEventAction;
use App\Http\Requests\Calendar\StoreCalendarRequest;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class CalendarController extends Controller
{
    public function __construct(
        private readonly CreateCalendarEventAction $createCalendarEventAction,
        private readonly UpdateCalendarEventAction $updateCalendarEventAction,
        private readonly DeleteCalendarEventAction $deleteCalendarEventAction,
    ) {}

    // ========================
    // Views
    // ========================

    public function index(): InertiaResponse
    {
        return inertia('Calendar/Index');
    }

    public function create(Request $request): InertiaResponse
    {
        return inertia('Calendar/Create', ['type' => $request->input('type')]);
    }

    public function edit(Calendar $calendar): InertiaResponse
    {
        return inertia('Calendar/Edit', compact('calendar'));
    }

    // ========================
    // CRUD
    // ========================

    public function store(StoreCalendarRequest $request): RedirectResponse
    {
        $this->createCalendarEventAction->execute($request->validated());

        return to_route('calendars.index');
    }

    public function update(StoreCalendarRequest $request, Calendar $calendar): RedirectResponse
    {
        $this->updateCalendarEventAction->execute($calendar, $request->validated());

        return to_route('calendars.index');
    }

    public function destroy(Request $request, Calendar $calendar): RedirectResponse
    {
        $mode = $request->query('deleteOption', 'Este');
        $this->deleteCalendarEventAction->execute($calendar, $mode);

        return to_route('calendars.index');
    }

    // ========================
    // API
    // ========================

    public function fetchMonthReminders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year'  => ['required', 'integer', 'min:2020'],
        ]);

        $reminders = Calendar::forUser()
            ->byMonth($validated['month'], $validated['year'])
            ->get();

        return response()->json(['reminders' => $reminders]);
    }
}