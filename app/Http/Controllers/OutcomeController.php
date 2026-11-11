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
use App\Services\Outcomes\OutcomeSummaryService;
use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
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
        private readonly OutcomeSummaryService $outcomeSummaryService,
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
            'type' => $request->string('type')->toString() ?: null,
        ];

        $outcomesQuery = $this->applyFilters(Outcome::forUser(), $filters);

        $filtered = [
            'count' => (clone $outcomesQuery)->count(),
            'total' => round((float) (clone $outcomesQuery)->sum('amount'), 2),
        ];

        $outcomes = $outcomesQuery->latest('id')->paginate($perPage)->withQueryString();
        $outcomes->through(fn (Outcome $outcome) => $outcome->append('split_total_amount'));

        $recurring_outcomes = RecurringOutcome::forUser()
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

        $summary = $this->outcomeSummaryService->build($request->user());

        return inertia('Outcome/Index', compact('outcomes', 'recurring_outcomes', 'summary', 'filters', 'filtered'));
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
            ->when($filters['payment_method'], fn (Builder $inner) => $inner->where('payment_method', $filters['payment_method']))
            ->when($filters['type'] === 'credit', fn (Builder $inner) => $inner->where('is_credit', true))
            ->when($filters['type'] === 'cash', fn (Builder $inner) => $inner->where('is_credit', false));
    }

    public function create(): InertiaResponse
    {
        return inertia('Outcome/Create');
    }

    public function edit(Outcome $outcome): InertiaResponse
    {
        $this->authorizeOwner($outcome);

        // El formulario necesita el monto total del gasto compartido (no solo la parte del creador)
        // para que el reparto vuelva a calcularse sobre la misma base.
        $outcome->append('split_total_amount');

        return inertia('Outcome/Edit', compact('outcome'));
    }

    // ========================
    // CRUD
    // ========================

    public function show(Outcome $outcome): InertiaResponse
    {
        $this->authorizeOwner($outcome);

        return inertia('Outcome/Show', compact('outcome'));
    }

    public function store(StoreOutcomeRequest $request): RedirectResponse
    {
        $this->createOutcomeAction->execute($request->validated());

        return to_route('outcomes.index');
    }

    public function update(UpdateOutcomeRequest $request, Outcome $outcome): RedirectResponse
    {
        $this->authorizeOwner($outcome);

        $this->updateOutcomeAction->execute($outcome, $request->validated());

        return to_route('outcomes.index');
    }

    public function destroy(Outcome $outcome): RedirectResponse
    {
        $this->authorizeOwner($outcome);

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
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_url' => $user->profile_photo_url,
        ]);

        return response()->json(['linked_users' => $linkedUsers]);
    }

    // ========================
    // Helpers
    // ========================

    private function authorizeOwner(Outcome $outcome): void
    {
        abort_if($outcome->user_id !== auth()->id(), 403);
    }
}
