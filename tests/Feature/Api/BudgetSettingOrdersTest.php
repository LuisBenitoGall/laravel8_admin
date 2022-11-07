<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\BudgetSettings as BudgetSetting;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BudgetSettingOrdersTest extends TestCase
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
    public function it_gets_budget_setting_orders()
    {
        $budgetSetting = BudgetSetting::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'budget_template_id' => $budgetSetting->id,
            ]);

        $response = $this->getJson(
            route('api.budget-settings.orders.index', $budgetSetting)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_budget_setting_orders()
    {
        $budgetSetting = BudgetSetting::factory()->create();
        $data = Order::factory()
            ->make([
                'budget_template_id' => $budgetSetting->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.budget-settings.orders.store', $budgetSetting),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($budgetSetting->id, $order->budget_template_id);
    }
}
