<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\BudgetPatterns as BudgetPattern;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BudgetPatternOrdersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_budget_pattern_orders()
    {
        $budgetPattern = BudgetPattern::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'budget_pattern_id' => $budgetPattern->id,
            ]);

        $response = $this->getJson(
            route('api.budget-patterns.orders.index', $budgetPattern)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_budget_pattern_orders()
    {
        $budgetPattern = BudgetPattern::factory()->create();
        $data = Order::factory()
            ->make([
                'budget_pattern_id' => $budgetPattern->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.budget-patterns.orders.store', $budgetPattern),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($budgetPattern->id, $order->budget_pattern_id);
    }
}
