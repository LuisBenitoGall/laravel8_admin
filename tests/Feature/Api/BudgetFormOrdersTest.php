<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\BudgetForm;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BudgetFormOrdersTest extends TestCase
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
    public function it_gets_budget_form_orders()
    {
        $budgetForm = BudgetForm::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'form_id' => $budgetForm->id,
            ]);

        $response = $this->getJson(
            route('api.budget-forms.orders.index', $budgetForm)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_budget_form_orders()
    {
        $budgetForm = BudgetForm::factory()->create();
        $data = Order::factory()
            ->make([
                'form_id' => $budgetForm->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.budget-forms.orders.store', $budgetForm),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($budgetForm->id, $order->form_id);
    }
}
