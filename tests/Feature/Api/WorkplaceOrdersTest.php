<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\Workplace;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkplaceOrdersTest extends TestCase
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
    public function it_gets_workplace_orders()
    {
        $workplace = Workplace::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'provider_workplace_id' => $workplace->id,
            ]);

        $response = $this->getJson(
            route('api.workplaces.orders.index', $workplace)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_workplace_orders()
    {
        $workplace = Workplace::factory()->create();
        $data = Order::factory()
            ->make([
                'provider_workplace_id' => $workplace->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.workplaces.orders.store', $workplace),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($workplace->id, $order->provider_workplace_id);
    }
}
