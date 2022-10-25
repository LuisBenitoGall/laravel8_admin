<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Town;

use App\Models\Province;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TownTest extends TestCase
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
    public function it_gets_towns_list()
    {
        $towns = Town::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.towns.index'));

        $response->assertOk()->assertSee($towns[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_town()
    {
        $data = Town::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.towns.store'), $data);

        $this->assertDatabaseHas('towns', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_town()
    {
        $town = Town::factory()->create();

        $province = Province::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'status' => $this->faker->randomNumber(0),
            'province_id' => $province->id,
        ];

        $response = $this->putJson(route('api.towns.update', $town), $data);

        $data['id'] = $town->id;

        $this->assertDatabaseHas('towns', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_town()
    {
        $town = Town::factory()->create();

        $response = $this->deleteJson(route('api.towns.destroy', $town));

        $this->assertSoftDeleted($town);

        $response->assertNoContent();
    }
}
