<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\CostCenters as CostCenter;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CostCenterOrdersTest extends TestCase
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
    public function it_gets_cost_center_orders()
    {
        $costCenter = CostCenter::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'provider_cost_center_id' => $costCenter->id,
            ]);

        $response = $this->getJson(
            route('api.cost-centers.orders.index', $costCenter)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_cost_center_orders()
    {
        $costCenter = CostCenter::factory()->create();
        $data = Order::factory()
            ->make([
                'provider_cost_center_id' => $costCenter->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.cost-centers.orders.store', $costCenter),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($costCenter->id, $order->provider_cost_center_id);
    }
}
