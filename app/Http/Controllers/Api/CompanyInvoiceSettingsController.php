<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceSettingResource;
use App\Http\Resources\InvoiceSettingCollection;

class CompanyInvoiceSettingsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $invoiceSettings = $company
            ->invoiceSettings()
            ->latest()
            ->paginate();

        return new InvoiceSettingCollection($invoiceSettings);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', InvoiceSetting::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'conditions' => ['required', 'max:255', 'string'],
            'payment_method' => ['nullable', 'max:255', 'string'],
            'observations' => ['nullable', 'max:255', 'string'],
            'featured' => ['nullable', 'numeric'],
            'status' => ['nullable', 'numeric'],
        ]);

        $invoiceSetting = $company->invoiceSettings()->create($validated);

        return new InvoiceSettingResource($invoiceSetting);
    }
}
