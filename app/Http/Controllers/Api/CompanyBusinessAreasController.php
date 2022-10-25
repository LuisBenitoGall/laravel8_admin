<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessAreaResource;
use App\Http\Resources\BusinessAreaCollection;

class CompanyBusinessAreasController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $search = $request->get('search', '');

        $businessAreas = $company
            ->businessAreas()
            ->search($search)
            ->latest()
            ->paginate();

        return new BusinessAreaCollection($businessAreas);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', BusinessArea::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'status' => ['required', 'numeric'],
        ]);

        $businessArea = $company->businessAreas()->create($validated);

        return new BusinessAreaResource($businessArea);
    }
}
