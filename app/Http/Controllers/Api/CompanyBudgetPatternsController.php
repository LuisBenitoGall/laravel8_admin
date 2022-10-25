<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetPatternResource;
use App\Http\Resources\BudgetPatternCollection;

class CompanyBudgetPatternsController extends Controller
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

        $budgetPatterns = $company
            ->budgetPatterns()
            ->search($search)
            ->latest()
            ->paginate();

        return new BudgetPatternCollection($budgetPatterns);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', BudgetPattern::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'pattern' => ['required', 'max:255', 'string'],
            'yearly_reset' => ['required', 'boolean'],
            'status' => ['required', 'numeric'],
        ]);

        $budgetPattern = $company->budgetPatterns()->create($validated);

        return new BudgetPatternResource($budgetPattern);
    }
}
