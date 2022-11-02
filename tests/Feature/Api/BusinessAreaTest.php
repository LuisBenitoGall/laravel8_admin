<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\BusinessAreas as BusinessArea;

use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessAreaTest extends TestCase
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
    public function it_gets_business_areas_list()
    {
        $businessAreas = BusinessArea::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.business-areas.index'));

        $response->assertOk()->assertSee($businessAreas[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_business_area()
    {
        $data = BusinessArea::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.business-areas.store'), $data);

        $this->assertDatabaseHas('business_areas', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_business_area()
    {
        $businessArea = BusinessArea::factory()->create();

        $company = Company::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'status' => $this->faker->randomNumber(0),
            'company_id' => $company->id,
        ];

        $response = $this->putJson(
            route('api.business-areas.update', $businessArea),
            $data
        );

        $data['id'] = $businessArea->id;

        $this->assertDatabaseHas('business_areas', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_business_area()
    {
        $businessArea = BusinessArea::factory()->create();

        $response = $this->deleteJson(
            route('api.business-areas.destroy', $businessArea)
        );

        $this->assertDeleted($businessArea);

        $response->assertNoContent();
    }
}
