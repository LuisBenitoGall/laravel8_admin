<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\BudgetPatterns as BudgetPattern;

use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BudgetPatternTest extends TestCase
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
    public function it_gets_budget_patterns_list()
    {
        $budgetPatterns = BudgetPattern::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.budget-patterns.index'));

        $response->assertOk()->assertSee($budgetPatterns[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_budget_pattern()
    {
        $data = BudgetPattern::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.budget-patterns.store'), $data);

        $this->assertDatabaseHas('budget_patterns', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_budget_pattern()
    {
        $budgetPattern = BudgetPattern::factory()->create();

        $company = Company::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'pattern' => $this->faker->text(255),
            'yearly_reset' => $this->faker->boolean,
            'status' => $this->faker->randomNumber(0),
            'company_id' => $company->id,
        ];

        $response = $this->putJson(
            route('api.budget-patterns.update', $budgetPattern),
            $data
        );

        $data['id'] = $budgetPattern->id;

        $this->assertDatabaseHas('budget_patterns', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_budget_pattern()
    {
        $budgetPattern = BudgetPattern::factory()->create();

        $response = $this->deleteJson(
            route('api.budget-patterns.destroy', $budgetPattern)
        );

        $this->assertSoftDeleted($budgetPattern);

        $response->assertNoContent();
    }
}
