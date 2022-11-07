<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyOrdersTest extends TestCase
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
    public function it_gets_company_orders()
    {
        $company = Company::factory()->create();
        $orders = Order::factory()
            ->count(2)
            ->create([
                'final_customer_id' => $company->id,
            ]);

        $response = $this->getJson(
            route('api.companies.orders.index', $company)
        );

        $response->assertOk()->assertSee($orders[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company_orders()
    {
        $company = Company::factory()->create();
        $data = Order::factory()
            ->make([
                'final_customer_id' => $company->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.companies.orders.store', $company),
            $data
        );

        $this->assertDatabaseHas('orders', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $order = Order::latest('id')->first();

        $this->assertEquals($company->id, $order->final_customer_id);
    }
}
