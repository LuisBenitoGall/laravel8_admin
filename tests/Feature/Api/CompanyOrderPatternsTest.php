<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\OrderPattern;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyOrderPatternsTest extends TestCase
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
    public function it_gets_company_order_patterns()
    {
        $company = Company::factory()->create();
        $orderPatterns = OrderPattern::factory()
            ->count(2)
            ->create([
                'company_id' => $company->id,
            ]);

        $response = $this->getJson(
            route('api.companies.order-patterns.index', $company)
        );

        $response->assertOk()->assertSee($orderPatterns[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company_order_patterns()
    {
        $company = Company::factory()->create();
        $data = OrderPattern::factory()
            ->make([
                'company_id' => $company->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.companies.order-patterns.store', $company),
            $data
        );

        $this->assertDatabaseHas('order_patterns', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $orderPattern = OrderPattern::latest('id')->first();

        $this->assertEquals($company->id, $orderPattern->company_id);
    }
}
