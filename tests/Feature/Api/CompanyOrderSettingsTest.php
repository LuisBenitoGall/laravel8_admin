<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\OrderSetting;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyOrderSettingsTest extends TestCase
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
    public function it_gets_company_order_settings()
    {
        $company = Company::factory()->create();
        $orderSettings = OrderSetting::factory()
            ->count(2)
            ->create([
                'company_id' => $company->id,
            ]);

        $response = $this->getJson(
            route('api.companies.order-settings.index', $company)
        );

        $response->assertOk()->assertSee($orderSettings[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company_order_settings()
    {
        $company = Company::factory()->create();
        $data = OrderSetting::factory()
            ->make([
                'company_id' => $company->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.companies.order-settings.store', $company),
            $data
        );

        $this->assertDatabaseHas('order_settings', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $orderSetting = OrderSetting::latest('id')->first();

        $this->assertEquals($company->id, $orderSetting->company_id);
    }
}
