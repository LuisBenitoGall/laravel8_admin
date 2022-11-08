<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderSettings as OrderSetting;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderSettingOrdersTest extends TestCase
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
    public function it_gets_order_setting_orders()
    {
        $orderSetting = OrderSetting::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'order_template_id' => $orderSetting->id,
            ]);

        $response = $this->getJson(
            route('api.order-settings.orders.index', $orderSetting)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_order_setting_orders()
    {
        $orderSetting = OrderSetting::factory()->create();
        $data = Order::factory()
            ->make([
                'order_template_id' => $orderSetting->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.order-settings.orders.store', $orderSetting),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($orderSetting->id, $order->order_template_id);
    }
}
