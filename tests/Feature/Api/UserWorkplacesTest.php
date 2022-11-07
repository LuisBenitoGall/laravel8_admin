<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Workplace;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserWorkplacesTest extends TestCase
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
    public function it_gets_user_workplaces()
    {
        $user = User::factory()->create();
        $workplace = Workplace::factory()->create();

        $user->workplaces()->attach($workplace);

        $response = $this->getJson(route('api.users.workplaces.index', $user));

        $response->assertOk()->assertSee($workplace->name);
    }

    /**
     * @test
     */
    public function it_can_attach_workplaces_to_user()
    {
        $user = User::factory()->create();
        $workplace = Workplace::factory()->create();

        $response = $this->postJson(
            route('api.users.workplaces.store', [$user, $workplace])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $user
                ->workplaces()
                ->where('workplaces.id', $workplace->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_workplaces_from_user()
    {
        $user = User::factory()->create();
        $workplace = Workplace::factory()->create();

        $response = $this->deleteJson(
            route('api.users.workplaces.store', [$user, $workplace])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $user
                ->workplaces()
                ->where('workplaces.id', $workplace->id)
                ->exists()
        );
    }
}
