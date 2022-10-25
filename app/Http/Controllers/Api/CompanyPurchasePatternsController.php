<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PurchasePatternResource;
use App\Http\Resources\PurchasePatternCollection;

class CompanyPurchasePatternsController extends Controller
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

        $purchasePatterns = $company
            ->purchasePatterns()
            ->search($search)
            ->latest()
            ->paginate();

        return new PurchasePatternCollection($purchasePatterns);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', PurchasePattern::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'pattern' => ['required', 'max:255', 'string'],
            'yearly_reset' => ['required', 'boolean'],
            'status' => ['required', 'numeric'],
        ]);

        $purchasePattern = $company->purchasePatterns()->create($validated);

        return new PurchasePatternResource($purchasePattern);
    }
}
