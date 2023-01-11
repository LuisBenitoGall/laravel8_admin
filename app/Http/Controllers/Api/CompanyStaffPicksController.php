<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\CompanyModules;
use App\Models\StaffPick;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffPickResource;
use App\Http\Resources\StaffPickCollection;

class CompanyStaffPicksController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);
        $slug = 'timetables';
        $companies = CompanyModules::select('companies.*')
            ->join('companies', 'company_modules.company_id', '=', 'companies.id')
            ->join('modules', 'company_modules.module_id', '=', 'modules.id')
            ->where('modules.slug', $slug)
            ->where('companies.status', 1)
            ->get();

        $staffPicks = StaffPick::whereHas('company', function ($q) use ($companies) {
            $q->whereIn('id', $companies->pluck('id'));
        })->where('status', true)
            ->latest()
            ->paginate();
        //  dd($companies->pluck('id'));
        /*  $staffPicks = $company
              ->staffPicks()
              ->latest()
              ->paginate();
*/
        return new StaffPickCollection($staffPicks);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', StaffPick::class);

        $validated = $request->validate([
            'name' => ['nullable', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'excerpt' => ['nullable', 'max:255', 'string'],
            'content' => ['nullable', 'max:255', 'string'],
            'published_at' => ['required', 'date'],
            'published_end' => ['required', 'date'],
            'created_by' => ['required', 'exists:users,id'],
            'updated_by' => ['required', 'exists:users,id'],
        ]);

        $staffPick = $company->staffPicks()->create($validated);

        return new StaffPickResource($staffPick);
    }
}
