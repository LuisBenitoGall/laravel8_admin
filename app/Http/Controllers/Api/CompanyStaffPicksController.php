<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
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

        $staffPicks = $company
            ->staffPicks()
            ->latest()
            ->paginate();

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
