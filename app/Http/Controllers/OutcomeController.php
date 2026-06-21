<?php

namespace App\Http\Controllers;

use App\Actions\Outcomes\CreateOutcomeAction;
use App\Actions\Outcomes\DeleteOutcomeAction;
use App\Actions\Outcomes\MassUpdateOutcomeAction;
use App\Actions\Outcomes\UpdateOutcomeAction;
use App\Http\Requests\Outcomes\MassiveDeleteOutcomeRequest;
use App\Http\Requests\Outcomes\StoreOutcomeRequest;
use App\Http\Requests\Outcomes\UpdateOutcomeRequest;
use App\Models\Outcome;
use App\Models\RecurringOutcome;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class OutcomeController extends Controller
{
    public function __construct(
        private readonly CreateOutcomeAction $createOutcomeAction,
        private readonly UpdateOutcomeAction $updateOutcomeAction,
        private readonly DeleteOutcomeAction $deleteOutcomeAction,
        private readonly MassUpdateOutcomeAction $massUpdateOutcomeAction,
        private readonly SearchService $searchService,
    ) {}

    // ========================
    // Views
    // ========================

    public function index(): InertiaResponse
    {
        $outcomes = Outcome::forUser()->latest('id')->paginate(50);
        $recurring_outcomes = RecurringOutcome::forUser()->latest('id')->paginate(50);

        return inertia('Outcome/Index', compact('outcomes', 'recurring_outcomes'));
    }

    public function create(): InertiaResponse
    {
        return inertia('Outcome/Create');
    }

    public function edit(Outcome $outcome): InertiaResponse
    {
        return inertia('Outcome/Edit', compact('outcome'));
    }

    // ========================
    // CRUD
    // ========================

    public function show(Outcome $outcome): InertiaResponse
    {
        return inertia('Outcome/Show', compact('outcome'));
    }

    public function store(StoreOutcomeRequest $request): RedirectResponse
    {
        $this->createOutcomeAction->execute($request->validated());

        return to_route('outcomes.index');
    }

    public function update(UpdateOutcomeRequest $request, Outcome $outcome): RedirectResponse
    {
        $this->updateOutcomeAction->execute($outcome, $request->validated());

        return to_route('outcomes.index');
    }

    public function destroy(Outcome $outcome): RedirectResponse
    {
        $this->deleteOutcomeAction->execute($outcome);

        return to_route('outcomes.index');
    }

    // ========================
    // Massive operations
    // ========================

    public function massiveDelete(MassiveDeleteOutcomeRequest $request): RedirectResponse
    {
        $this->deleteOutcomeAction->executeBulk($request->validated()['ids']);

        return to_route('outcomes.index');
    }

    public function massiveUpdate(Request $request): RedirectResponse
    {
        $ids = array_column($request->input('selections', []), 'id');
        $this->massUpdateOutcomeAction->execute($ids, $request->only(['category', 'payment_method']));

        return to_route('outcomes.index');
    }

    // ========================
    // Search & helpers
    // ========================

    public function getMatches(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        $items = $this->searchService->searchForUser(
            Outcome::class,
            $query,
            ['id', 'concept', 'amount', 'category', 'created_at', 'payment_method']
        );

        return response()->json(['items' => $items]);
    }

    public function getLinkedUsers(): JsonResponse
    {
        $linkedUsers = auth()->user()->linkedUsers()->map(fn ($user) => [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'profile_photo_url' => $user->profile_photo_url,
        ]);

        return response()->json(['linked_users' => $linkedUsers]);
    }
}
