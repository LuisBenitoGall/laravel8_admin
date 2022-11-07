<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\Workplace;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyWorkplacesTest extends TestCase
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
    public function it_gets_company_workplaces()
    {
        $company = Company::factory()->create();
        $workplaces = Workplace::factory()
            ->count(2)
            ->create([
                'company_id' => $company->id,
            ]);

        $response = $this->getJson(
            route('api.companies.workplaces.index', $company)
        );

        $response->assertOk()->assertSee($workplaces[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_company_workplaces()
    {
        $company = Company::factory()->create();
        $data = Workplace::factory()
            ->make([
                'company_id' => $company->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.companies.workplaces.store', $company),
            $data
        );

        $this->assertDatabaseHas('workplaces', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $workplace = Workplace::latest('id')->first();

        $this->assertEquals($company->id, $workplace->company_id);
    }
}
