<?php

namespace Tests\Feature;

use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class IncomeIndexSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_index_exposes_summary_metrics(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        Income::create([
            'concept' => 'Venta mostrador',
            'amount' => 500,
            'category' => 'Ventas',
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        Income::create([
            'concept' => 'Venta mes anterior',
            'amount' => 250,
            'category' => 'Ventas',
            'user_id' => $user->id,
            'created_at' => now()->subMonthNoOverflow(),
        ]);

        $this->actingAs($user)
            ->get(route('incomes.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Income/Index')
                ->where('summary.count', 2)
                ->where('summary.total', 750)
                ->where('summary.current_month', 500)
                ->where('summary.top_category.key', 'Ventas')
                ->where('filtered.count', 2)
                ->where('filtered.total', 750)
                ->etc());
    }

    public function test_income_index_filters_are_applied_on_the_server(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        Income::create([
            'concept' => 'Venta mostrador',
            'amount' => 500,
            'category' => 'Ventas',
            'payment_method' => 'Efectivo',
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        Income::create([
            'concept' => 'Renta local',
            'amount' => 900,
            'category' => 'Renta',
            'payment_method' => 'Transferencia',
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('incomes.index', ['q' => 'mostrador', 'category' => 'Ventas']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.q', 'mostrador')
                ->where('filters.category', 'Ventas')
                ->where('filtered.count', 1)
                ->where('filtered.total', 500)
                ->has('incomes.data', 1)
                ->where('incomes.data.0.concept', 'Venta mostrador')
                ->etc());
    }
}
