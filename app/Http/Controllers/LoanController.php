<?php

namespace App\Http\Controllers;

use App\Actions\Loans\CreateLoanAction;
use App\Actions\Loans\DeleteLoanAction;
use App\Actions\Loans\UpdateLoanAction;
use App\Http\Requests\Loans\StoreLoanRequest;
use App\Http\Requests\Loans\UpdateLoanRequest;
use App\Models\Loan;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class LoanController extends Controller
{
    public function __construct(
        private readonly CreateLoanAction $createLoanAction,
        private readonly UpdateLoanAction $updateLoanAction,
        private readonly DeleteLoanAction $deleteLoanAction,
        private readonly SearchService $searchService,
    ) {}

    // ========================
    // Views
    // ========================

    public function index(): InertiaResponse
    {
        $loans_for_me = Loan::with('payments')
            ->forUser()
            ->byType('Recibido')
            ->latest('id')
            ->paginate(50);

        $loans_given = Loan::with('payments')
            ->forUser()
            ->byType('Otorgado')
            ->latest('id')
            ->paginate(50);

        return inertia('Loan/Index', compact('loans_for_me', 'loans_given'));
    }

    public function create(): InertiaResponse
    {
        return inertia('Loan/Create');
    }

    public function show(Loan $loan): InertiaResponse
    {
        $loan->load('payments');
        $loans = Loan::forUser()->latest()->get(['id', 'type', 'beneficiary_name', 'lender_name', 'amount']);

        return inertia('Loan/Show', compact('loan', 'loans'));
    }

    public function edit(Loan $loan): InertiaResponse
    {
        return inertia('Loan/Edit', compact('loan'));
    }

    public function externalView(string $encrypted_id): InertiaResponse
    {
        $decoded_id = base64_decode($encrypted_id);
        $loan = Loan::with('payments')->findOrFail($decoded_id);

        return inertia('Loan/ExternalView', compact('loan'));
    }

    // ========================
    // CRUD
    // ========================

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $loan = $this->createLoanAction->execute($request->validated());

        return to_route('loans.show', $loan->id);
    }

    public function update(UpdateLoanRequest $request, Loan $loan): RedirectResponse
    {
        $this->updateLoanAction->execute($loan, $request->validated());

        return to_route('loans.index');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->deleteLoanAction->execute($loan);

        return to_route('loans.index');
    }

    // ========================
    // Massive operations
    // ========================

    public function massiveDelete(Request $request): RedirectResponse
    {
        $ids = array_column($request->input('loans', []), 'id');
        $this->deleteLoanAction->executeBulk($ids);

        return to_route('loans.index');
    }

    // ========================
    // Search
    // ========================

    public function getMatches(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        $items = $this->searchService->searchForUser(
            Loan::class,
            $query,
            ['id', 'beneficiary_name', 'lender_name', 'amount', 'loan_date', 'profitability_type']
        );

        return response()->json(['items' => $items]);
    }
}
