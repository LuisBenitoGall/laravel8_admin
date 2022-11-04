<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderPatterns as OrderPattern;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderPatternOrdersTest extends TestCase
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
    public function it_gets_order_pattern_orders()
    {
        $orderPattern = OrderPattern::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'order_pattern_id' => $orderPattern->id,
            ]);

        $response = $this->getJson(
            route('api.order-patterns.orders.index', $orderPattern)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_order_pattern_orders()
    {
        $orderPattern = OrderPattern::factory()->create();
        $data = Order::factory()
            ->make([
                'order_pattern_id' => $orderPattern->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.order-patterns.orders.store', $orderPattern),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($orderPattern->id, $order->order_pattern_id);
    }
}
