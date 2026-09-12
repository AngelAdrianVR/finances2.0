<?php

namespace App\Http\Controllers;

use App\Actions\Calendar\CreateCalendarEventAction;
use App\Actions\Calendar\DeleteCalendarEventAction;
use App\Actions\Calendar\UpdateCalendarEventAction;
use App\Http\Requests\Calendar\StoreCalendarRequest;
use App\Models\Calendar;
use App\Models\RecurringIncome;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;
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
        private readonly CalendarService $calendarService,
    ) {}

    // ========================
    // Views
    // ========================

    public function index(): InertiaResponse
    {
        // Refleja en el calendario los ingresos y gastos recurrentes activos del usuario.
        $userId = auth()->id();

        RecurringIncome::forUser($userId)->active()->get()->each(
            fn (RecurringIncome $income) => $this->calendarService->ensureRecurringEventsFromModel($income, 'Ingreso recurrente')
        );

        RecurringOutcome::forUser($userId)->active()->get()->each(
            fn (RecurringOutcome $outcome) => $this->calendarService->ensureRecurringEventsFromModel($outcome, 'Gasto fijo')
        );

        return inertia('Calendar/Index');
    }

    public function create(Request $request): InertiaResponse
    {
        return inertia('Calendar/Create', ['type' => $request->input('type')]);
    }

    public function edit(Calendar $calendar): InertiaResponse
    {
        $this->authorizeOwner($calendar);

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
        $this->authorizeOwner($calendar);

        $this->updateCalendarEventAction->execute($calendar, $request->validated());

        return to_route('calendars.index');
    }

    public function destroy(Request $request, Calendar $calendar): RedirectResponse|JsonResponse
    {
        $this->authorizeOwner($calendar);

        $mode = $request->query('deleteOption', 'Este');
        $this->deleteCalendarEventAction->execute($calendar, $mode);

        // El calendario elimina via axios (JSON); el resto navega de vuelta al listado.
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['deleted' => true]);
        }

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
            ->where('status', '!=', 'Cancelado') // las ocurrencias canceladas no se muestran
            ->get();

        return response()->json(['reminders' => $reminders]);
    }

    // ========================
    // Helpers
    // ========================

    /**
     * Ensure the authenticated user only operates on their own calendar events.
     */
    private function authorizeOwner(Calendar $calendar): void
    {
        abort_if($calendar->user_id !== auth()->id(), 403);
    }
}