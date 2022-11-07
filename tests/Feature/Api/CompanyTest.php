<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyTest extends TestCase
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
    public function it_gets_companies_list()
    {
        $companies = Company::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.companies.index'));

        $response->assertOk()->assertSee($companies[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company()
    {
        $data = Company::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.companies.store'), $data);

        $this->assertDatabaseHas('companies', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_company()
    {
        $company = Company::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'tradename' => $this->faker->text(255),
            'acronym' => $this->faker->text(255),
            'logo' => $this->faker->text(255),
            'nif' => $this->faker->text(255),
            'ccc' => $this->faker->text(255),
            'status' => $this->faker->randomNumber(0),
        ];

        $response = $this->putJson(
            route('api.companies.update', $company),
            $data
        );

        $data['id'] = $company->id;

        $this->assertDatabaseHas('companies', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_company()
    {
        $company = Company::factory()->create();

        $response = $this->deleteJson(route('api.companies.destroy', $company));

        $this->assertDeleted($company);

        $response->assertNoContent();
    }
}
