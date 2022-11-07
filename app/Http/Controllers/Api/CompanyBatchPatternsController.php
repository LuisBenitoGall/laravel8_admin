<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BatchPatternResource;
use App\Http\Resources\BatchPatternCollection;

class CompanyBatchPatternsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $batchPatterns = $company
            ->batchPatterns()
            ->latest()
            ->paginate();

        return new BatchPatternCollection($batchPatterns);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', BatchPattern::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'pattern' => ['required', 'max:255', 'string'],
            'yearly_reset' => ['required', 'boolean'],
            'status' => ['required', 'numeric'],
        ]);

        $batchPattern = $company->batchPatterns()->create($validated);

        return new BatchPatternResource($batchPattern);
    }
}
