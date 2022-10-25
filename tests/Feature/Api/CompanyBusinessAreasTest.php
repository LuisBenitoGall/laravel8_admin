<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\BusinessArea;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyBusinessAreasTest extends TestCase
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
    public function it_gets_company_business_areas()
    {
        $company = Company::factory()->create();
        $businessAreas = BusinessArea::factory()
            ->count(2)
            ->create([
                'company_id' => $company->id,
            ]);

        $response = $this->getJson(
            route('api.companies.business-areas.index', $company)
        );

        $response->assertOk()->assertSee($businessAreas[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company_business_areas()
    {
        $company = Company::factory()->create();
        $data = BusinessArea::factory()
            ->make([
                'company_id' => $company->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.companies.business-areas.store', $company),
            $data
        );

        $this->assertDatabaseHas('business_areas', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $businessArea = BusinessArea::latest('id')->first();

        $this->assertEquals($company->id, $businessArea->company_id);
    }
}
