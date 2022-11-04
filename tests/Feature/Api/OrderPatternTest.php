<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\OrderPatterns as OrderPattern;

use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderPatternTest extends TestCase
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
    public function it_gets_order_patterns_list()
    {
        $orderPatterns = OrderPattern::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.order-patterns.index'));

        $response->assertOk()->assertSee($orderPatterns[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_order_pattern()
    {
        $data = OrderPattern::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.order-patterns.store'), $data);

        $this->assertDatabaseHas('order_patterns', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_order_pattern()
    {
        $orderPattern = OrderPattern::factory()->create();

        $company = Company::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'pattern' => $this->faker->text(255),
            'yearly_reset' => $this->faker->boolean,
            'status' => $this->faker->randomNumber(0),
            'company_id' => $company->id,
        ];

        $response = $this->putJson(
            route('api.order-patterns.update', $orderPattern),
            $data
        );

        $data['id'] = $orderPattern->id;

        $this->assertDatabaseHas('order_patterns', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_order_pattern()
    {
        $orderPattern = OrderPattern::factory()->create();

        $response = $this->deleteJson(
            route('api.order-patterns.destroy', $orderPattern)
        );

        $this->assertSoftDeleted($orderPattern);

        $response->assertNoContent();
    }
}
