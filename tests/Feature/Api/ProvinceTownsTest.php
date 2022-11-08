<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Town;
use App\Models\Province;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProvinceTownsTest extends TestCase
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
    public function it_gets_province_towns()
    {
        $province = Province::factory()->create();
        $towns = Town::factory()
            ->count(2)
            ->create([
                'province_id' => $province->id,
            ]);

        $response = $this->getJson(
            route('api.provinces.towns.index', $province)
        );

        $response->assertOk()->assertSee($towns[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_province_towns()
    {
        $province = Province::factory()->create();
        $data = Town::factory()
            ->make([
                'province_id' => $province->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.provinces.towns.store', $province),
            $data
        );

        $this->assertDatabaseHas('towns', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $town = Town::latest('id')->first();

        $this->assertEquals($province->id, $town->province_id);
    }
}
