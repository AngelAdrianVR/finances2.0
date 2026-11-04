<?php

namespace Tests\Feature;

use App\Models\Income;
use App\Models\Outcome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }

    public function test_overview_returns_period_totals_and_series(): void
    {
        $user = User::factory()->create(['total_money' => 1500]);

        Income::create([
            'concept' => 'Nómina',
            'amount' => 800,
            'category' => 'Nómina',
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        Outcome::create([
            'concept' => 'Súper',
            'amount' => 300,
            'category' => 'Alimentos y bebidas',
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('dashboard.fetch-overview'), [
            'periodicity' => 'Mensual',
            'period' => now()->toDateString(),
        ]);

        $response->assertOk()
            ->assertJsonPath('totals.income', 800)
            ->assertJsonPath('totals.outcome', 300)
            ->assertJsonPath('totals.net', 500)
            ->assertJsonPath('balance', 1500)
            ->assertJsonPath('categories.outcome.0.key', 'Alimentos y bebidas')
            ->assertJsonStructure([
                'range' => ['periodicity', 'label', 'bucket'],
                'kpis' => ['savings_rate', 'daily_average_outcome', 'projected_outcome'],
                'trend' => ['labels', 'income', 'outcome', 'net'],
                'incomes',
                'outcomes',
                'loans',
            ]);

        $this->assertCount(count(range(1, now()->daysInMonth)), $response->json('trend.labels'));
    }
}
