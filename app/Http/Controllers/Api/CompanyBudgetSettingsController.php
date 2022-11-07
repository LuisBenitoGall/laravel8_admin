<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetSettingResource;
use App\Http\Resources\BudgetSettingCollection;

class CompanyBudgetSettingsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $budgetSettings = $company
            ->budgetSettings()
            ->latest()
            ->paginate();

        return new BudgetSettingCollection($budgetSettings);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', BudgetSetting::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'conditions' => ['nullable', 'max:255', 'string'],
            'payment_method' => ['nullable', 'max:255', 'string'],
            'observations' => ['nullable', 'max:255', 'string'],
            'footer_legend' => ['nullable', 'max:255', 'string'],
            'featured' => ['required', 'numeric'],
            'status' => ['required', 'max:255'],
        ]);

        $budgetSetting = $company->budgetSettings()->create($validated);

        return new BudgetSettingResource($budgetSetting);
    }
}
