<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\PurchasePattern;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyPurchasePatternsTest extends TestCase
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
    public function it_gets_company_purchase_patterns()
    {
        $company = Company::factory()->create();
        $purchasePatterns = PurchasePattern::factory()
            ->count(2)
            ->create([
                'company_id' => $company->id,
            ]);

        $response = $this->getJson(
            route('api.companies.purchase-patterns.index', $company)
        );

        $response->assertOk()->assertSee($purchasePatterns[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company_purchase_patterns()
    {
        $company = Company::factory()->create();
        $data = PurchasePattern::factory()
            ->make([
                'company_id' => $company->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.companies.purchase-patterns.store', $company),
            $data
        );

        $this->assertDatabaseHas('purchase_patterns', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $purchasePattern = PurchasePattern::latest('id')->first();

        $this->assertEquals($company->id, $purchasePattern->company_id);
    }
}
