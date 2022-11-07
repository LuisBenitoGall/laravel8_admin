<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductPatternResource;
use App\Http\Resources\ProductPatternCollection;

class CompanyProductPatternsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $productPatterns = $company
            ->productPatterns()
            ->latest()
            ->paginate();

        return new ProductPatternCollection($productPatterns);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', ProductPattern::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'pattern' => ['required', 'max:255', 'string'],
            'status' => ['required', 'numeric'],
        ]);

        $productPattern = $company->productPatterns()->create($validated);

        return new ProductPatternResource($productPattern);
    }
}
