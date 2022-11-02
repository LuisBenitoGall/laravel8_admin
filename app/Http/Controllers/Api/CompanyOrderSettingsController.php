<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderSettingResource;
use App\Http\Resources\OrderSettingCollection;

class CompanyOrderSettingsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $orderSettings = $company
            ->orderSettings()
            ->latest()
            ->paginate();

        return new OrderSettingCollection($orderSettings);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', OrderSetting::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'conditions' => ['nullable', 'max:255', 'string'],
            'payment_method' => ['nullable', 'max:255', 'string'],
            'observations' => ['nullable', 'max:255', 'string'],
            'footer_legend' => ['nullable', 'max:255', 'string'],
            'featured' => ['required', 'numeric'],
            'status' => ['required', 'numeric'],
        ]);

        $orderSetting = $company->orderSettings()->create($validated);

        return new OrderSettingResource($orderSetting);
    }
}
