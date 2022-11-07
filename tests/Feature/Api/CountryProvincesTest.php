<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Country;
use App\Models\Province;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CountryProvincesTest extends TestCase
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
    public function it_gets_country_provinces()
    {
        $country = Country::factory()->create();
        $provinces = Province::factory()
            ->count(2)
            ->create([
                'country_id' => $country->id,
            ]);

        $response = $this->getJson(
            route('api.countries.provinces.index', $country)
        );

        $response->assertOk()->assertSee($provinces[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_country_provinces()
    {
        $country = Country::factory()->create();
        $data = Province::factory()
            ->make([
                'country_id' => $country->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.countries.provinces.store', $country),
            $data
        );

        $this->assertDatabaseHas('provinces', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $province = Province::latest('id')->first();

        $this->assertEquals($country->id, $province->country_id);
    }
}
