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
use App\Services\SearchService;
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
    ) {}

    // ========================
    // Views
    // ========================

    public function index(): InertiaResponse
    {
        $incomes = Income::forUser()->latest('id')->paginate(50);
        $recurring_incomes = RecurringIncome::forUser()->latest('id')->paginate(50);

        return inertia('Income/Index', compact('incomes', 'recurring_incomes'));
    }

    public function create(): InertiaResponse
    {
        return inertia('Income/Create');
    }

    public function edit(Income $income): InertiaResponse
    {
        return inertia('Income/Edit', compact('income'));
    }

    // ========================
    // CRUD
    // ========================

    public function show(Income $income): InertiaResponse
    {
        return inertia('Income/Show', compact('income'));
    }

    public function store(StoreIncomeRequest $request): RedirectResponse
    {
        $this->createIncomeAction->execute($request->validated());

        return to_route('incomes.index');
    }

    public function update(UpdateIncomeRequest $request, Income $income): RedirectResponse
    {
        $this->updateIncomeAction->execute($income, $request->validated());

        return to_route('incomes.index');
    }

    public function destroy(Income $income): RedirectResponse
    {
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
