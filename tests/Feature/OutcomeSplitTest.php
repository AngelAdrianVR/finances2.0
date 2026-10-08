<?php

namespace Tests\Feature;

use App\Actions\Outcomes\CreateOutcomeAction;
use App\Actions\Outcomes\DeleteOutcomeAction;
use App\Actions\Outcomes\UpdateOutcomeAction;
use App\Models\Outcome;
use App\Models\User;
use App\Models\UserLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutcomeSplitTest extends TestCase
{
    use RefreshDatabase;

    private const INITIAL_MONEY = 1000.0;

    /**
     * A creator with two linked users, all with the same available balance.
     *
     * @return array{0: User, 1: User, 2: User}
     */
    private function makeParticipants(): array
    {
        $creator = User::factory()->create(['total_money' => self::INITIAL_MONEY]);
        $first = User::factory()->create(['total_money' => self::INITIAL_MONEY]);
        $second = User::factory()->create(['total_money' => self::INITIAL_MONEY]);

        foreach ([$first, $second] as $linked) {
            UserLink::create([
                'user_id' => $creator->id,
                'linked_user_id' => $linked->id,
                'status' => 'accepted',
            ]);
        }

        return [$creator, $first, $second];
    }

    /**
     * Payload shaped like the validated request data.
     */
    private function outcomeData(array $overrides = []): array
    {
        return array_merge([
            'amount' => 300,
            'concept' => 'Cena compartida',
            'category' => 'Alimentos y bebidas',
            'payment_method' => 'Efectivo',
            'description' => null,
            'created_at' => now()->toDateString(),
            'is_credit' => false,
            'payment_due_date' => null,
            'split_enabled' => false,
            'split_with' => [],
            'is_recurring_outcome' => false,
            'periodicity' => null,
        ], $overrides);
    }

    private function assertMoney(float $expected, float $actual, string $message = ''): void
    {
        $this->assertEqualsWithDelta($expected, $actual, 0.001, $message);
    }

    public function test_creating_a_shared_outcome_discounts_the_balance_of_every_participant(): void
    {
        [$creator, $first, $second] = $this->makeParticipants();
        $this->actingAs($creator);

        $outcome = app(CreateOutcomeAction::class)->execute($this->outcomeData([
            'amount' => 300,
            'split_enabled' => true,
            'split_with' => [$first->id, $second->id],
        ]));

        // El creador guarda solo su parte proporcional (300 / 3 personas).
        $this->assertMoney(100.0, (float) $outcome->amount, 'El creador debe guardar su parte proporcional.');
        $this->assertTrue((bool) $outcome->split_enabled);

        $this->assertMoney(self::INITIAL_MONEY - 100.0, (float) $creator->fresh()->total_money, 'Saldo del creador.');
        $this->assertMoney(self::INITIAL_MONEY - 100.0, (float) $first->fresh()->total_money, 'Saldo del primer vinculado debe descontarse.');
        $this->assertMoney(self::INITIAL_MONEY - 100.0, (float) $second->fresh()->total_money, 'Saldo del segundo vinculado debe descontarse.');

        // Un registro para el creador y uno por cada usuario vinculado.
        $this->assertSame(3, Outcome::count());
        $this->assertSame(2, Outcome::where('parent_outcome_id', $outcome->id)->count());
    }

    public function test_editing_a_shared_outcome_refunds_the_old_split_and_applies_the_new_one(): void
    {
        [$creator, $first, $second] = $this->makeParticipants();
        $this->actingAs($creator);

        $split = ['split_enabled' => true, 'split_with' => [$first->id, $second->id]];
        $outcome = app(CreateOutcomeAction::class)->execute($this->outcomeData(['amount' => 300] + $split));

        $updated = app(UpdateOutcomeAction::class)->execute($outcome, $this->outcomeData([
            'amount' => 600,
            'concept' => 'Cena compartida',
        ] + $split));

        $this->assertMoney(200.0, (float) $updated->amount, 'La nueva parte proporcional debe ser 600 / 3.');

        // Ningun monto debe contarse dos veces: el reparto anterior se reembolsa antes de aplicar el nuevo.
        $this->assertMoney(self::INITIAL_MONEY - 200.0, (float) $creator->fresh()->total_money, 'Saldo del creador.');
        $this->assertMoney(self::INITIAL_MONEY - 200.0, (float) $first->fresh()->total_money, 'Saldo del primer vinculado.');
        $this->assertMoney(self::INITIAL_MONEY - 200.0, (float) $second->fresh()->total_money, 'Saldo del segundo vinculado.');

        $this->assertSame(3, Outcome::count(), 'Los registros anteriores deben reemplazarse, no duplicarse.');
        $this->assertSame(2, Outcome::where('parent_outcome_id', $outcome->id)->count());
    }

    public function test_editing_a_shared_outcome_can_remove_the_split(): void
    {
        [$creator, $first, $second] = $this->makeParticipants();
        $this->actingAs($creator);

        $outcome = app(CreateOutcomeAction::class)->execute($this->outcomeData([
            'amount' => 300,
            'split_enabled' => true,
            'split_with' => [$first->id, $second->id],
        ]));

        $updated = app(UpdateOutcomeAction::class)->execute($outcome, $this->outcomeData([
            'amount' => 150,
            'split_enabled' => false,
            'split_with' => [],
        ]));

        $this->assertMoney(150.0, (float) $updated->amount, 'Sin reparto, el monto se guarda completo.');
        $this->assertFalse((bool) $updated->split_enabled);

        $this->assertMoney(self::INITIAL_MONEY - 150.0, (float) $creator->fresh()->total_money, 'Saldo del creador.');
        $this->assertMoney(self::INITIAL_MONEY, (float) $first->fresh()->total_money, 'El primer vinculado debe recuperar su saldo.');
        $this->assertMoney(self::INITIAL_MONEY, (float) $second->fresh()->total_money, 'El segundo vinculado debe recuperar su saldo.');

        $this->assertSame(1, Outcome::count(), 'Al quitar el reparto deben eliminarse los registros de los vinculados.');
    }

    public function test_deleting_a_shared_outcome_refunds_every_participant(): void
    {
        [$creator, $first, $second] = $this->makeParticipants();
        $this->actingAs($creator);

        $outcome = app(CreateOutcomeAction::class)->execute($this->outcomeData([
            'amount' => 300,
            'split_enabled' => true,
            'split_with' => [$first->id, $second->id],
        ]));

        app(DeleteOutcomeAction::class)->execute($outcome);

        $this->assertSame(0, Outcome::count());
        $this->assertMoney(self::INITIAL_MONEY, (float) $creator->fresh()->total_money, 'Saldo del creador debe restaurarse.');
        $this->assertMoney(self::INITIAL_MONEY, (float) $first->fresh()->total_money, 'Saldo del primer vinculado debe restaurarse.');
        $this->assertMoney(self::INITIAL_MONEY, (float) $second->fresh()->total_money, 'Saldo del segundo vinculado debe restaurarse.');
    }

    public function test_bulk_deleting_shared_outcomes_refunds_every_participant(): void
    {
        [$creator, $first, $second] = $this->makeParticipants();
        $this->actingAs($creator);

        $outcome = app(CreateOutcomeAction::class)->execute($this->outcomeData([
            'amount' => 300,
            'split_enabled' => true,
            'split_with' => [$first->id, $second->id],
        ]));

        $restored = app(DeleteOutcomeAction::class)->executeBulk([$outcome->id]);

        $this->assertMoney(100.0, $restored, 'Solo se restaura la parte del creador en el borrado masivo.');
        $this->assertSame(0, Outcome::count());
        $this->assertMoney(self::INITIAL_MONEY, (float) $creator->fresh()->total_money, 'Saldo del creador debe restaurarse.');
        $this->assertMoney(self::INITIAL_MONEY, (float) $first->fresh()->total_money, 'Saldo del primer vinculado debe restaurarse.');
        $this->assertMoney(self::INITIAL_MONEY, (float) $second->fresh()->total_money, 'Saldo del segundo vinculado debe restaurarse.');
    }

    public function test_editing_a_regular_outcome_moves_the_balance_in_the_right_direction(): void
    {
        [$creator] = $this->makeParticipants();
        $this->actingAs($creator);

        $outcome = app(CreateOutcomeAction::class)->execute($this->outcomeData(['amount' => 100]));
        $this->assertMoney(self::INITIAL_MONEY - 100.0, (float) $creator->fresh()->total_money);

        // Aumentar el gasto resta mas saldo.
        $outcome = app(UpdateOutcomeAction::class)->execute($outcome, $this->outcomeData(['amount' => 300]));
        $this->assertMoney(self::INITIAL_MONEY - 300.0, (float) $creator->fresh()->total_money, 'Un gasto mayor debe restar mas saldo.');

        // Reducir el gasto devuelve la diferencia.
        $outcome = app(UpdateOutcomeAction::class)->execute($outcome, $this->outcomeData(['amount' => 50]));
        $this->assertMoney(self::INITIAL_MONEY - 50.0, (float) $creator->fresh()->total_money, 'Un gasto menor debe devolver saldo.');
    }

    public function test_split_total_amount_accessor_rebuilds_the_full_amount(): void
    {
        [$creator, $first, $second] = $this->makeParticipants();
        $this->actingAs($creator);

        $outcome = app(CreateOutcomeAction::class)->execute($this->outcomeData([
            'amount' => 300,
            'split_enabled' => true,
            'split_with' => [$first->id, $second->id],
        ]));

        $this->assertMoney(300.0, $outcome->split_total_amount, 'El formulario de edicion debe recibir el monto total.');
    }
}
