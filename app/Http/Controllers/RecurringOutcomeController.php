<?php

namespace App\Http\Controllers;

use App\Models\RecurringOutcome;
use App\Services\CalendarService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecurringOutcomeController extends Controller
{
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
        return inertia('RecurringOutcome/Edit', compact('recurring_outcome'));
    }

    // ========================
    // CRUD
    // ========================

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0', 'max:999999'],
            'category' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'concept' => ['required', 'string', 'max:50'],
            'periodicity' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $data['user_id'] = auth()->id();
        $data['created_at'] = $request->input('created_at', now());

        $recurring = RecurringOutcome::create($data);

        $this->calendarService->generateRecurringEvents([
            'type' => 'Gasto fijo',
            'title' => $data['concept'],
            'amount' => $data['amount'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'periodicity' => $data['periodicity'],
            'payment_method' => $data['payment_method'] ?? null,
            'user_id' => $data['user_id'],
            'created_at' => $data['created_at'],
        ]);

        return to_route('outcomes.index', ['currentTab' => 2]);
    }

    public function update(Request $request, RecurringOutcome $recurring_outcome): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0', 'max:999999'],
            'category' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'concept' => ['required', 'string', 'max:50'],
            'periodicity' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $this->calendarService->removeByTitle($recurring_outcome->concept);

        $recurring_outcome->update($data);

        $this->calendarService->generateRecurringEvents([
            'type' => 'Gasto fijo',
            'title' => $data['concept'],
            'amount' => $data['amount'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'periodicity' => $data['periodicity'],
            'payment_method' => $data['payment_method'] ?? null,
            'user_id' => auth()->id(),
            'created_at' => $request->input('created_at', $recurring_outcome->created_at),
        ]);

        return to_route('outcomes.index', ['currentTab' => 2]);
    }

    public function destroy(RecurringOutcome $recurring_outcome): RedirectResponse
    {
        $this->calendarService->removeByTitle($recurring_outcome->concept);
        $recurring_outcome->delete();

        return to_route('outcomes.index');
    }

    // ========================
    // Massive & toggle
    // ========================

    public function massiveDelete(Request $request): RedirectResponse
    {
        $ids = array_column($request->input('recurring_outcomes', []), 'id');
        $items = RecurringOutcome::forUser()->whereIn('id', $ids)->get();

        foreach ($items as $item) {
            $this->calendarService->removeByTitle($item->concept);
        }

        RecurringOutcome::forUser()->whereIn('id', $ids)->delete();

        return to_route('outcomes.index');
    }

    public function toggleStatus(RecurringOutcome $recurring_outcome): JsonResponse
    {
        $recurring_outcome->toggle();

        return response()->json(['is_active' => $recurring_outcome->is_active]);
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
