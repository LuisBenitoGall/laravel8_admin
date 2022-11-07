<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderPatternResource;
use App\Http\Resources\OrderPatternCollection;

class CompanyOrderPatternsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $orderPatterns = $company
            ->orderPatterns()
            ->latest()
            ->paginate();

        return new OrderPatternCollection($orderPatterns);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', OrderPattern::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'pattern' => ['required', 'max:255', 'string'],
            'yearly_reset' => ['required', 'boolean'],
            'status' => ['required', 'numeric'],
        ]);

        $orderPattern = $company->orderPatterns()->create($validated);

        return new OrderPatternResource($orderPattern);
    }
}
