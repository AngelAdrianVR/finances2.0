<?php

namespace App\Http\Controllers;

use App\Actions\Incomes\CreateIncomeAction;
use App\Actions\Incomes\DeleteIncomeAction;
use App\Actions\Incomes\MassUpdateIncomeAction;
use App\Actions\Incomes\UpdateIncomeAction;
use App\Http\Requests\Incomes\MassiveDeleteIncomeRequest;
use App\Http\Requests\Incomes\MassiveUpdateIncomeRequest;
use App\Http\Requests\Incomes\StoreIncomeRequest;
use App\Http\Requests\Incomes\UpdateIncomeRequest;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Services\Incomes\IncomeSummaryService;
use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class IncomeController extends Controller
{
    public function __construct(
        private readonly CreateIncomeAction $createIncomeAction,
        private readonly UpdateIncomeAction $updateIncomeAction,
        private readonly DeleteIncomeAction $deleteIncomeAction,
        private readonly MassUpdateIncomeAction $massUpdateIncomeAction,
        private readonly SearchService $searchService,
        private readonly IncomeSummaryService $incomeSummaryService,
    ) {}

    // ========================
    // Views
    // ========================

    public function index(Request $request): InertiaResponse
    {
        // Tamaño de página configurable (10, 20, 50 o 100).
        $perPage = $request->integer('per_page', 50);
        if (! in_array($perPage, [10, 20, 50, 100], true)) {
            $perPage = 50;
        }

        $filters = [
            'q' => trim($request->string('q')->toString()),
            'category' => $request->string('category')->toString() ?: null,
            'payment_method' => $request->string('payment_method')->toString() ?: null,
        ];

        $incomesQuery = $this->applyFilters(Income::forUser(), $filters);

        $filtered = [
            'count' => (clone $incomesQuery)->count(),
            'total' => round((float) (clone $incomesQuery)->sum('amount'), 2),
        ];

        $incomes = $incomesQuery->latest('id')->paginate($perPage)->withQueryString();

        $recurring_incomes = RecurringIncome::forUser()
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $like = '%'.$filters['q'].'%';

                return $query->where(function ($inner) use ($like) {
                    $inner->where('concept', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        $summary = $this->incomeSummaryService->build($request->user());

        return inertia('Income/Index', compact('incomes', 'recurring_incomes', 'summary', 'filters', 'filtered'));
    }

    /**
     * Applies the search and filter criteria shared by the index queries.
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] !== '', function (Builder $inner) use ($filters) {
                $like = '%'.$filters['q'].'%';

                return $inner->where(function (Builder $nested) use ($like) {
                    $nested->where('concept', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->when($filters['category'], fn (Builder $inner) => $inner->where('category', $filters['category']))
            ->when($filters['payment_method'], fn (Builder $inner) => $inner->where('payment_method', $filters['payment_method']));
    }

    public function create(): InertiaResponse
    {
        return inertia('Income/Create');
    }

    public function edit(Income $income): InertiaResponse
    {
        $this->authorizeOwner($income);

        return inertia('Income/Edit', compact('income'));
    }

    // ========================
    // CRUD
    // ========================

    public function show(Income $income): InertiaResponse
    {
        $this->authorizeOwner($income);

        return inertia('Income/Show', compact('income'));
    }

    public function store(StoreIncomeRequest $request): RedirectResponse
    {
        $this->createIncomeAction->execute($request->validated());

        return to_route('incomes.index');
    }

    public function update(UpdateIncomeRequest $request, Income $income): RedirectResponse
    {
        $this->authorizeOwner($income);

        $this->updateIncomeAction->execute($income, $request->validated());

        return to_route('incomes.index');
    }

    public function destroy(Income $income): RedirectResponse
    {
        $this->authorizeOwner($income);

        $this->deleteIncomeAction->execute($income);

        return to_route('incomes.index');
    }

    // ========================
    // Massive operations
    // ========================

    public function massiveDelete(MassiveDeleteIncomeRequest $request): RedirectResponse
    {
        $this->deleteIncomeAction->executeBulk($request->validated()['ids']);

        return to_route('incomes.index');
    }

    public function massiveUpdate(MassiveUpdateIncomeRequest $request): RedirectResponse
    {
        $ids = array_column($request->validated()['selections'], 'id');
        $this->massUpdateIncomeAction->execute($ids, $request->validated());

        return to_route('incomes.index');
    }

    // ========================
    // Helpers
    // ========================

    private function authorizeOwner(Income $income): void
    {
        abort_if($income->user_id !== auth()->id(), 403);
    }

    // ========================
    // Search
    // ========================

    public function getMatches(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        $items = $this->searchService->searchForUser(
            Income::class,
            $query,
            ['id', 'concept', 'amount', 'category', 'created_at', 'payment_method']
        );

        return response()->json(['items' => $items]);
    }
}
