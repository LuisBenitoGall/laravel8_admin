<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\PurchasePattern;

use App\Models\Company;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchasePatternTest extends TestCase
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
    public function it_gets_purchase_patterns_list()
    {
        $purchasePatterns = PurchasePattern::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.purchase-patterns.index'));

        $response->assertOk()->assertSee($purchasePatterns[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_purchase_pattern()
    {
        $data = PurchasePattern::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(
            route('api.purchase-patterns.store'),
            $data
        );

        $this->assertDatabaseHas('purchase_patterns', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_purchase_pattern()
    {
        $purchasePattern = PurchasePattern::factory()->create();

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
            route('api.purchase-patterns.update', $purchasePattern),
            $data
        );

        $data['id'] = $purchasePattern->id;

        $this->assertDatabaseHas('purchase_patterns', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_purchase_pattern()
    {
        $purchasePattern = PurchasePattern::factory()->create();

        $response = $this->deleteJson(
            route('api.purchase-patterns.destroy', $purchasePattern)
        );

        $this->assertSoftDeleted($purchasePattern);

        $response->assertNoContent();
    }
}
