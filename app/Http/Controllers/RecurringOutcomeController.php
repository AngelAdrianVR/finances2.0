<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\RecurringOutcome;
use App\Services\CalendarService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecurringOutcomeController extends Controller
{
    private const CALENDAR_TYPE = 'Gasto fijo';

    public function __construct(
        private readonly SearchService $searchService,
        private readonly CalendarService $calendarService,
    ) {}

    // ========================
    // Views
    // ========================

    public function create()
    {
        return inertia('RecurringOutcome/Create');
    }

    public function edit(RecurringOutcome $recurring_outcome)
    {
        $this->authorizeOwner($recurring_outcome);

        return inertia('RecurringOutcome/Edit', compact('recurring_outcome'));
    }

    // ========================
    // CRUD
    // ========================

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['user_id'] = auth()->id();

        $recurring = RecurringOutcome::create($data);

        // Schedule only the upcoming occurrences (never regenerate past dates).
        $this->calendarService->generateRecurringEventsFromModel($recurring, self::CALENDAR_TYPE, true);

        return to_route('outcomes.index', ['currentTab' => 2]);
    }

    public function update(Request $request, RecurringOutcome $recurring_outcome): RedirectResponse
    {
        $data = $this->validatedData($request);

        $this->authorizeOwner($recurring_outcome);

        // Remove upcoming events of the previous configuration before regenerating.
        $oldConcept = $recurring_outcome->getOriginal('concept');
        $this->calendarService->removeByTitle(
            $oldConcept,
            self::CALENDAR_TYPE,
            $recurring_outcome->user_id,
            now()->toDateString()
        );

        $recurring_outcome->update($data);

        // Only schedule again when the recurring item is active.
        if ($recurring_outcome->is_active) {
            $this->calendarService->removeByTitle(
                $recurring_outcome->concept,
                self::CALENDAR_TYPE,
                $recurring_outcome->user_id,
                now()->toDateString()
            );
            $this->calendarService->generateRecurringEventsFromModel($recurring_outcome, self::CALENDAR_TYPE, true);
        }

        return to_route('outcomes.index', ['currentTab' => 2]);
    }

    public function destroy(RecurringOutcome $recurring_outcome): RedirectResponse
    {
        $this->authorizeOwner($recurring_outcome);

        $this->calendarService->removeByTitle(
            $recurring_outcome->concept,
            self::CALENDAR_TYPE,
            $recurring_outcome->user_id,
            now()->toDateString()
        );
        $recurring_outcome->delete();

        return to_route('outcomes.index', ['currentTab' => 2]);
    }

    // ========================
    // Massive & toggle
    // ========================

    public function massiveDelete(Request $request): RedirectResponse
    {
        $ids = array_column($request->input('recurring_outcomes', []), 'id');

        if (! empty($ids)) {
            $items = RecurringOutcome::forUser()->whereIn('id', $ids)->get(['id', 'concept']);

            if ($items->isNotEmpty()) {
                // Single bulk delete for all upcoming calendar events of the removed items.
                Calendar::where('user_id', auth()->id())
                    ->where('type', self::CALENDAR_TYPE)
                    ->whereIn('title', $items->pluck('concept'))
                    ->where('date', '>=', now()->toDateString())
                    ->delete();

                RecurringOutcome::forUser()->whereIn('id', $ids)->delete();
            }
        }

        return to_route('outcomes.index', ['currentTab' => 2]);
    }

    public function toggleStatus(RecurringOutcome $recurring_outcome): JsonResponse
    {
        $this->authorizeOwner($recurring_outcome);

        $recurring_outcome->toggle();

        if ($recurring_outcome->is_active) {
            // Clean any leftover future event, then schedule from the next occurrence on.
            $this->calendarService->removeByTitle(
                $recurring_outcome->concept,
                self::CALENDAR_TYPE,
                $recurring_outcome->user_id,
                now()->toDateString()
            );
            $this->calendarService->generateRecurringEventsFromModel($recurring_outcome, self::CALENDAR_TYPE, true);
        } else {
            // Stop future automatic registrations.
            $this->calendarService->removeByTitle(
                $recurring_outcome->concept,
                self::CALENDAR_TYPE,
                $recurring_outcome->user_id,
                now()->toDateString()
            );
        }

        return response()->json(['is_active' => $recurring_outcome->is_active]);
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

    private function authorizeOwner(RecurringOutcome $recurring_outcome): void
    {
        abort_if($recurring_outcome->user_id !== auth()->id(), 403);
    }

    // ========================
    // Search
    // ========================

    public function getMatches(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        $items = $this->searchService->searchForUser(
            RecurringOutcome::class,
            $query,
            ['id', 'concept', 'amount', 'category', 'created_at', 'payment_method']
        );

        return response()->json(['items' => $items]);
    }
}
