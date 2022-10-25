<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\BusinessArea;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessAreaOrdersTest extends TestCase
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
    public function it_gets_business_area_orders()
    {
        $businessArea = BusinessArea::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'provider_business_area_id' => $businessArea->id,
            ]);

        $response = $this->getJson(
            route('api.business-areas.orders.index', $businessArea)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_business_area_orders()
    {
        $businessArea = BusinessArea::factory()->create();
        $data = Order::factory()
            ->make([
                'provider_business_area_id' => $businessArea->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.business-areas.orders.store', $businessArea),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals(
            $businessArea->id,
            $order->provider_business_area_id
        );
    }
}
