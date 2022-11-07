<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkOrderPatternResource;
use App\Http\Resources\WorkOrderPatternCollection;

class CompanyWorkOrderPatternsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $workOrderPatterns = $company
            ->workOrderPatterns()
            ->latest()
            ->paginate();

        return new WorkOrderPatternCollection($workOrderPatterns);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', WorkOrderPattern::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'pattern' => ['required', 'max:255', 'string'],
            'yearly_reset' => ['required', 'boolean'],
            'status' => ['required', 'numeric'],
        ]);

        $workOrderPattern = $company->workOrderPatterns()->create($validated);

        return new WorkOrderPatternResource($workOrderPattern);
    }
}
