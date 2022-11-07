<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Province;

use App\Models\Country;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProvinceTest extends TestCase
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
    public function it_gets_provinces_list()
    {
        $provinces = Province::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.provinces.index'));

        $response->assertOk()->assertSee($provinces[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_province()
    {
        $data = Province::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.provinces.store'), $data);

        $this->assertDatabaseHas('provinces', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_province()
    {
        $province = Province::factory()->create();

        $country = Country::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'status' => $this->faker->randomNumber(0),
            'country_id' => $country->id,
        ];

        $response = $this->putJson(
            route('api.provinces.update', $province),
            $data
        );

        $data['id'] = $province->id;

        $this->assertDatabaseHas('provinces', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_province()
    {
        $province = Province::factory()->create();

        $response = $this->deleteJson(
            route('api.provinces.destroy', $province)
        );

        $this->assertSoftDeleted($province);

        $response->assertNoContent();
    }
}
