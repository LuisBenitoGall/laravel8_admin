<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\BudgetSetting;

use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BudgetSettingTest extends TestCase
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
    public function it_gets_budget_settings_list()
    {
        $budgetSettings = BudgetSetting::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.budget-settings.index'));

        $response->assertOk()->assertSee($budgetSettings[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_budget_setting()
    {
        $data = BudgetSetting::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.budget-settings.store'), $data);

        $this->assertDatabaseHas('budget_settings', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_budget_setting()
    {
        $budgetSetting = BudgetSetting::factory()->create();

        $company = Company::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'conditions' => $this->faker->text,
            'payment_method' => $this->faker->text,
            'observations' => $this->faker->text,
            'footer_legend' => $this->faker->text,
            'featured' => $this->faker->randomNumber(0),
            'status' => $this->faker->numberBetween(0, 8388607),
            'company_id' => $company->id,
        ];

        $response = $this->putJson(
            route('api.budget-settings.update', $budgetSetting),
            $data
        );

        $data['id'] = $budgetSetting->id;

        $this->assertDatabaseHas('budget_settings', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_budget_setting()
    {
        $budgetSetting = BudgetSetting::factory()->create();

        $response = $this->deleteJson(
            route('api.budget-settings.destroy', $budgetSetting)
        );

        $this->assertDeleted($budgetSetting);

        $response->assertNoContent();
    }
}
