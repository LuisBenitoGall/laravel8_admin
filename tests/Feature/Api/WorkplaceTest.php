<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Workplace;

use App\Models\Town;
use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkplaceTest extends TestCase
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
    public function it_gets_workplaces_list()
    {
        $workplaces = Workplace::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.workplaces.index'));

        $response->assertOk()->assertSee($workplaces[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_workplace()
    {
        $data = Workplace::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.workplaces.store'), $data);

        $this->assertDatabaseHas('workplaces', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_workplace()
    {
        $workplace = Workplace::factory()->create();

        $company = Company::factory()->create();
        $town = Town::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'featured' => $this->faker->randomNumber(0),
            'logo' => $this->faker->text(255),
            'address' => $this->faker->text(255),
            'cp' => $this->faker->text(255),
            'nif' => $this->faker->text(255),
            'website' => $this->faker->text(255),
            'description' => $this->faker->sentence(15),
            'status' => $this->faker->randomNumber(0),
            'company_id' => $company->id,
            'town_id' => $town->id,
        ];

        $response = $this->putJson(
            route('api.workplaces.update', $workplace),
            $data
        );

        $data['id'] = $workplace->id;

        $this->assertDatabaseHas('workplaces', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_workplace()
    {
        $workplace = Workplace::factory()->create();

        $response = $this->deleteJson(
            route('api.workplaces.destroy', $workplace)
        );

        $this->assertSoftDeleted($workplace);

        $response->assertNoContent();
    }
}
