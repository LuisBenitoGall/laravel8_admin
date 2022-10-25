<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\CostCenter;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CostCenterTest extends TestCase
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
    public function it_gets_cost_centers_list()
    {
        $costCenters = CostCenter::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.cost-centers.index'));

        $response->assertOk()->assertSee($costCenters[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_cost_center()
    {
        $data = CostCenter::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.cost-centers.store'), $data);

        $this->assertDatabaseHas('cost_centers', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_cost_center()
    {
        $costCenter = CostCenter::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'status' => $this->faker->randomNumber(0),
        ];

        $response = $this->putJson(
            route('api.cost-centers.update', $costCenter),
            $data
        );

        $data['id'] = $costCenter->id;

        $this->assertDatabaseHas('cost_centers', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_cost_center()
    {
        $costCenter = CostCenter::factory()->create();

        $response = $this->deleteJson(
            route('api.cost-centers.destroy', $costCenter)
        );

        $this->assertSoftDeleted($costCenter);

        $response->assertNoContent();
    }
}
