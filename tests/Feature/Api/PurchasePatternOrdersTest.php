<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\PurchasePattern;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchasePatternOrdersTest extends TestCase
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
    public function it_gets_purchase_pattern_orders()
    {
        $purchasePattern = PurchasePattern::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'purchase_pattern_id' => $purchasePattern->id,
            ]);

        $response = $this->getJson(
            route('api.purchase-patterns.orders.index', $purchasePattern)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_purchase_pattern_orders()
    {
        $purchasePattern = PurchasePattern::factory()->create();
        $data = Order::factory()
            ->make([
                'purchase_pattern_id' => $purchasePattern->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.purchase-patterns.orders.store', $purchasePattern),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($purchasePattern->id, $order->purchase_pattern_id);
    }
}
