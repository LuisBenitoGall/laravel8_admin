<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Workplace;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkplaceUsersTest extends TestCase
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
    public function it_gets_workplace_users()
    {
        $workplace = Workplace::factory()->create();
        $user = User::factory()->create();

        $workplace->users()->attach($user);

        $response = $this->getJson(
            route('api.workplaces.users.index', $workplace)
        );

        $response->assertOk()->assertSee($user->name);
    }

    /**
     * @test
     */
    public function it_can_attach_users_to_workplace()
    {
        $workplace = Workplace::factory()->create();
        $user = User::factory()->create();

        $response = $this->postJson(
            route('api.workplaces.users.store', [$workplace, $user])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $workplace
                ->users()
                ->where('users.id', $user->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_users_from_workplace()
    {
        $workplace = Workplace::factory()->create();
        $user = User::factory()->create();

        $response = $this->deleteJson(
            route('api.workplaces.users.store', [$workplace, $user])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $workplace
                ->users()
                ->where('users.id', $user->id)
                ->exists()
        );
    }
}
