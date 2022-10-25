<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\BudgetSetting;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyBudgetSettingsTest extends TestCase
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
    public function it_gets_company_budget_settings()
    {
        $company = Company::factory()->create();
        $budgetSettings = BudgetSetting::factory()
            ->count(2)
            ->create([
                'company_id' => $company->id,
            ]);

        $response = $this->getJson(
            route('api.companies.budget-settings.index', $company)
        );

        $response->assertOk()->assertSee($budgetSettings[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company_budget_settings()
    {
        $company = Company::factory()->create();
        $data = BudgetSetting::factory()
            ->make([
                'company_id' => $company->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.companies.budget-settings.store', $company),
            $data
        );

        $this->assertDatabaseHas('budget_settings', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $budgetSetting = BudgetSetting::latest('id')->first();

        $this->assertEquals($company->id, $budgetSetting->company_id);
    }
}
