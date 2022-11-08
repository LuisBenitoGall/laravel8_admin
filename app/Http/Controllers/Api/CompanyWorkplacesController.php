<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkplaceResource;
use App\Http\Resources\WorkplaceCollection;

class CompanyWorkplacesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $workplaces = $company
            ->workplaces()
            ->latest()
            ->paginate();

        return new WorkplaceCollection($workplaces);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', Workplace::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'featured' => ['required', 'numeric'],
            'logo' => ['nullable', 'max:255', 'string'],
            'address' => ['nullable', 'max:255', 'string'],
            'cp' => ['nullable', 'max:255', 'string'],
            'town_id' => ['nullable', 'exists:towns,id'],
            'nif' => ['nullable', 'max:255', 'string'],
            'website' => ['nullable', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'status' => ['required', 'numeric'],
        ]);

        $workplace = $company->workplaces()->create($validated);

        return new WorkplaceResource($workplace);
    }
}
