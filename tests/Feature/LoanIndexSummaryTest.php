<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LoanIndexSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_index_exposes_summary_metrics(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $given = $this->createLoan($user, 'Otorgado', 1000, 'María López');

        Payment::create([
            'loan_id' => $given->id,
            'amount' => 350,
            'interest' => 50,
            'capital' => 300,
            'remaining' => 700,
            'payment_method' => 'Efectivo',
            'date' => now(),
        ]);

        $this->createLoan($user, 'Recibido', 500, 'Banco Azteca');

        $this->actingAs($user)
            ->get(route('loans.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Loan/Index')
                ->where('summary.given.count', 1)
                ->where('summary.given.total', 1000)
                ->where('summary.given.paid', 300)
                ->where('summary.given.remaining', 700)
                ->where('summary.given.progress', 30)
                ->where('summary.received.count', 1)
                ->where('summary.received.total', 500)
                ->where('summary.received.remaining', 500)
                ->has('loans_given.data', 1)
                ->has('loans_for_me.data', 1)
                ->etc());
    }

    public function test_loan_index_search_is_applied_on_the_server(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->createLoan($user, 'Otorgado', 1000, 'María López');
        $this->createLoan($user, 'Otorgado', 800, 'Carlos Ramírez');

        $this->actingAs($user)
            ->get(route('loans.index', ['q' => 'María']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.q', 'María')
                ->has('loans_given.data', 1)
                ->where('loans_given.data.0.beneficiary_name', 'María López')
                ->has('loans_for_me.data', 0)
                ->etc());
    }

    private function createLoan(User $user, string $type, float $amount, string $person): Loan
    {
        return Loan::create([
            'user_id' => $user->id,
            'type' => $type,
            'beneficiary_name' => $type === 'Otorgado' ? $person : null,
            'lender_name' => $type === 'Recibido' ? $person : null,
            'amount' => $amount,
            'status' => 'En curso',
            'loan_date' => now(),
            'automatic' => true,
            'is_for_me' => $type === 'Recibido',
        ]);
    }
}
