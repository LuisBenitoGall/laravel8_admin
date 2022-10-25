<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\BudgetPattern;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyBudgetPatternsTest extends TestCase
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
    public function it_gets_company_budget_patterns()
    {
        $company = Company::factory()->create();
        $budgetPatterns = BudgetPattern::factory()
            ->count(2)
            ->create([
                'company_id' => $company->id,
            ]);

        $response = $this->getJson(
            route('api.companies.budget-patterns.index', $company)
        );

        $response->assertOk()->assertSee($budgetPatterns[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company_budget_patterns()
    {
        $company = Company::factory()->create();
        $data = BudgetPattern::factory()
            ->make([
                'company_id' => $company->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.companies.budget-patterns.store', $company),
            $data
        );

        $this->assertDatabaseHas('budget_patterns', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $budgetPattern = BudgetPattern::latest('id')->first();

        $this->assertEquals($company->id, $budgetPattern->company_id);
    }
}
