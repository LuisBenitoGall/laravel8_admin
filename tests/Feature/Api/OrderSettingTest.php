<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\OrderSettings as OrderSetting;

use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderSettingTest extends TestCase
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
    public function it_gets_order_settings_list()
    {
        $orderSettings = OrderSetting::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.order-settings.index'));

        $response->assertOk()->assertSee($orderSettings[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_order_setting()
    {
        $data = OrderSetting::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.order-settings.store'), $data);

        $this->assertDatabaseHas('order_settings', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_order_setting()
    {
        $orderSetting = OrderSetting::factory()->create();

        $company = Company::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'conditions' => $this->faker->text,
            'payment_method' => $this->faker->text,
            'observations' => $this->faker->text,
            'footer_legend' => $this->faker->text,
            'featured' => $this->faker->randomNumber(0),
            'status' => $this->faker->randomNumber(0),
            'company_id' => $company->id,
        ];

        $response = $this->putJson(
            route('api.order-settings.update', $orderSetting),
            $data
        );

        $data['id'] = $orderSetting->id;

        $this->assertDatabaseHas('order_settings', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_order_setting()
    {
        $orderSetting = OrderSetting::factory()->create();

        $response = $this->deleteJson(
            route('api.order-settings.destroy', $orderSetting)
        );

        $this->assertSoftDeleted($orderSetting);

        $response->assertNoContent();
    }
}
