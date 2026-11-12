<?php

namespace Tests\Feature;

use App\Models\Outcome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OutcomeIndexSummaryTest extends TestCase
{
    use RefreshDatabase;

    private function makeOutcome(User $user, array $attributes = []): Outcome
    {
        return Outcome::create(array_merge([
            'concept' => 'Gasto de prueba',
            'amount' => 100,
            'category' => 'Compras',
            'payment_method' => 'Efectivo',
            'user_id' => $user->id,
            'created_at' => now(),
        ], $attributes));
    }

    public function test_outcome_index_exposes_summary_metrics(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->makeOutcome($user, ['concept' => 'Súper', 'amount' => 400, 'category' => 'Alimentos y bebidas']);
        $this->makeOutcome($user, [
            'concept' => 'Laptop a crédito',
            'amount' => 600,
            'is_credit' => true,
            'payment_due_date' => now()->addWeek(),
        ]);

        $this->actingAs($user)
            ->get(route('outcomes.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Outcome/Index')
                ->where('summary.count', 2)
                ->where('summary.total', 1000)
                ->where('summary.current_month', 1000)
                ->where('summary.credit_pending', 600)
                ->where('summary.credit_pending_count', 1)
                ->where('summary.top_category.key', 'Compras')
                ->where('filtered.count', 2)
                ->where('filtered.total', 1000)
                ->has('outcomes.data.0.split_total_amount')
                ->etc());
    }

    public function test_outcome_index_filters_are_applied_on_the_server(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->makeOutcome($user, ['concept' => 'Súper semanal', 'amount' => 400, 'category' => 'Alimentos y bebidas']);
        $this->makeOutcome($user, [
            'concept' => 'Laptop a crédito',
            'amount' => 600,
            'is_credit' => true,
            'payment_due_date' => now()->addWeek(),
        ]);

        $this->actingAs($user)
            ->get(route('outcomes.index', ['type' => 'credit']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.type', 'credit')
                ->where('filtered.count', 1)
                ->where('filtered.total', 600)
                ->has('outcomes.data', 1)
                ->where('outcomes.data.0.concept', 'Laptop a crédito')
                ->etc());

        $this->actingAs($user)
            ->get(route('outcomes.index', ['q' => 'súper', 'category' => 'Alimentos y bebidas']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('filtered.count', 1)
                ->where('filtered.total', 400)
                ->where('outcomes.data.0.concept', 'Súper semanal')
                ->etc());
    }
}
