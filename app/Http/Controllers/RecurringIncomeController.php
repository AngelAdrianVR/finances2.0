<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\RecurringIncome;
use App\Services\CalendarService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecurringIncomeController extends Controller
{
    private const CALENDAR_TYPE = 'Ingreso recurrente';

    public function __construct(
        private readonly SearchService $searchService,
        private readonly CalendarService $calendarService,
    ) {}

    // ========================
    // Views
    // ========================

    public function create()
    {
        return inertia('RecurringIncome/Create');
    }

    public function edit(RecurringIncome $recurring_income)
    {
        $this->authorizeOwner($recurring_income);

        return inertia('RecurringIncome/Edit', compact('recurring_income'));
    }

    // ========================
    // CRUD
    // ========================

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['user_id'] = auth()->id();

        $recurring = RecurringIncome::create($data);

        // Schedule only the upcoming occurrences (never regenerate past dates).
        $this->calendarService->generateRecurringEventsFromModel($recurring, self::CALENDAR_TYPE, true);

        return to_route('incomes.index', ['currentTab' => 2]);
    }

    public function update(Request $request, RecurringIncome $recurring_income): RedirectResponse
    {
        $data = $this->validatedData($request);

        $this->authorizeOwner($recurring_income);

        // Remove upcoming events of the previous configuration before regenerating.
        $oldConcept = $recurring_income->getOriginal('concept');
        $this->calendarService->removeByTitle(
            $oldConcept,
            self::CALENDAR_TYPE,
            $recurring_income->user_id,
            now()->toDateString()
        );

        $recurring_income->update($data);

        // Only schedule again when the recurring item is active.
        if ($recurring_income->is_active) {
            $this->calendarService->removeByTitle(
                $recurring_income->concept,
                self::CALENDAR_TYPE,
                $recurring_income->user_id,
                now()->toDateString()
            );
            $this->calendarService->generateRecurringEventsFromModel($recurring_income, self::CALENDAR_TYPE, true);
        }

        return to_route('incomes.index', ['currentTab' => 2]);
    }

    public function destroy(RecurringIncome $recurring_income): RedirectResponse
    {
        $this->authorizeOwner($recurring_income);

        $this->calendarService->removeByTitle(
            $recurring_income->concept,
            self::CALENDAR_TYPE,
            $recurring_income->user_id,
            now()->toDateString()
        );
        $recurring_income->delete();

        return to_route('incomes.index', ['currentTab' => 2]);
    }

    // ========================
    // Massive & toggle
    // ========================

    public function massiveDelete(Request $request): RedirectResponse
    {
        $ids = array_column($request->input('recurring_incomes', []), 'id');

        if (! empty($ids)) {
            $items = RecurringIncome::forUser()->whereIn('id', $ids)->get(['id', 'concept']);

            if ($items->isNotEmpty()) {
                // Single bulk delete for all upcoming calendar events of the removed items.
                Calendar::where('user_id', auth()->id())
                    ->where('type', self::CALENDAR_TYPE)
                    ->whereIn('title', $items->pluck('concept'))
                    ->where('date', '>=', now()->toDateString())
                    ->delete();

                RecurringIncome::forUser()->whereIn('id', $ids)->delete();
            }
        }

        return to_route('incomes.index', ['currentTab' => 2]);
    }

    public function toggleStatus(RecurringIncome $recurring_income): JsonResponse
    {
        $this->authorizeOwner($recurring_income);

        $recurring_income->toggle();

        if ($recurring_income->is_active) {
            // Clean any leftover future event, then schedule from the next occurrence on.
            $this->calendarService->removeByTitle(
                $recurring_income->concept,
                self::CALENDAR_TYPE,
                $recurring_income->user_id,
                now()->toDateString()
            );
            $this->calendarService->generateRecurringEventsFromModel($recurring_income, self::CALENDAR_TYPE, true);
        } else {
            // Stop future automatic registrations.
            $this->calendarService->removeByTitle(
                $recurring_income->concept,
                self::CALENDAR_TYPE,
                $recurring_income->user_id,
                now()->toDateString()
            );
        }

        return response()->json(['is_active' => $recurring_income->is_active]);
    }

    // ========================
    // Helpers
    // ========================

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'amount' => ['required', 'numeric', 'min:0', 'max:999999'],
            'category' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'concept' => ['required', 'string', 'max:50'],
            'periodicity' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'created_at' => ['sometimes', 'nullable', 'date'],
        ]);
    }

    private function authorizeOwner(RecurringIncome $recurring_income): void
    {
        abort_if($recurring_income->user_id !== auth()->id(), 403);
    }

    // ========================
    // Search
    // ========================

    public function getMatches(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        $items = $this->searchService->searchForUser(
            RecurringIncome::class,
            $query,
            ['id', 'concept', 'amount', 'category', 'created_at', 'payment_method']
        );

        return response()->json(['items' => $items]);
    }
}
