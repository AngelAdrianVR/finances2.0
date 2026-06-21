<?php

namespace App\Http\Controllers;

use App\Models\RecurringIncome;
use App\Services\CalendarService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecurringIncomeController extends Controller
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
        return inertia('RecurringIncome/Create');
    }

    public function edit(RecurringIncome $recurring_income)
    {
        return inertia('RecurringIncome/Edit', compact('recurring_income'));
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

        $recurring = RecurringIncome::create($data);

        $this->calendarService->generateRecurringEvents([
            'type' => 'Ingreso recurrente',
            'title' => $data['concept'],
            'amount' => $data['amount'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'periodicity' => $data['periodicity'],
            'payment_method' => $data['payment_method'] ?? null,
            'user_id' => $data['user_id'],
            'created_at' => $data['created_at'],
        ]);

        return to_route('incomes.index', ['currentTab' => 2]);
    }

    public function update(Request $request, RecurringIncome $recurring_income): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0', 'max:999999'],
            'category' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'concept' => ['required', 'string', 'max:50'],
            'periodicity' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        // Remove old calendar events
        $this->calendarService->removeByTitle($recurring_income->concept);

        $recurring_income->update($data);

        // Regenerate
        $this->calendarService->generateRecurringEvents([
            'type' => 'Ingreso recurrente',
            'title' => $data['concept'],
            'amount' => $data['amount'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'periodicity' => $data['periodicity'],
            'payment_method' => $data['payment_method'] ?? null,
            'user_id' => auth()->id(),
            'created_at' => $request->input('created_at', $recurring_income->created_at),
        ]);

        return to_route('incomes.index', ['currentTab' => 2]);
    }

    public function destroy(RecurringIncome $recurring_income): RedirectResponse
    {
        $this->calendarService->removeByTitle($recurring_income->concept);
        $recurring_income->delete();

        return to_route('incomes.index');
    }

    // ========================
    // Massive & toggle
    // ========================

    public function massiveDelete(Request $request): RedirectResponse
    {
        $ids = array_column($request->input('recurring_incomes', []), 'id');
        $items = RecurringIncome::forUser()->whereIn('id', $ids)->get();

        foreach ($items as $item) {
            $this->calendarService->removeByTitle($item->concept);
        }

        RecurringIncome::forUser()->whereIn('id', $ids)->delete();

        return to_route('incomes.index');
    }

    public function toggleStatus(RecurringIncome $recurring_income): JsonResponse
    {
        $recurring_income->toggle();

        return response()->json(['is_active' => $recurring_income->is_active]);
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
