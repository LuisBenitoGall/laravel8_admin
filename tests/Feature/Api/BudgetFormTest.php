<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\BudgetForm;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BudgetFormTest extends TestCase
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
    public function it_gets_budget_forms_list()
    {
        $budgetForms = BudgetForm::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.budget-forms.index'));

        $response->assertOk()->assertSee($budgetForms[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_budget_form()
    {
        $data = BudgetForm::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.budget-forms.store'), $data);

        $this->assertDatabaseHas('budget_forms', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_budget_form()
    {
        $budgetForm = BudgetForm::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'conditions' => $this->faker->text,
            'labor_journeys' => $this->faker->text,
            'status' => $this->faker->randomNumber(0),
        ];

        $response = $this->putJson(
            route('api.budget-forms.update', $budgetForm),
            $data
        );

        $data['id'] = $budgetForm->id;

        $this->assertDatabaseHas('budget_forms', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_budget_form()
    {
        $budgetForm = BudgetForm::factory()->create();

        $response = $this->deleteJson(
            route('api.budget-forms.destroy', $budgetForm)
        );

        $this->assertSoftDeleted($budgetForm);

        $response->assertNoContent();
    }
}
