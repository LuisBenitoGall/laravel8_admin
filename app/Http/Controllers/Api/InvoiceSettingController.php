<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\InvoiceSetting;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceSettingResource;
use App\Http\Resources\InvoiceSettingCollection;
use App\Http\Requests\InvoiceSettingStoreRequest;
use App\Http\Requests\InvoiceSettingUpdateRequest;

class InvoiceSettingController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', InvoiceSetting::class);

        //$search = $request->get('search', '');

        $invoiceSettings = InvoiceSetting::latest()->paginate();

        return new InvoiceSettingCollection($invoiceSettings);
    }

    /**
     * @param \App\Http\Requests\InvoiceSettingStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceSettingStoreRequest $request)
    {
        // $this->authorize('create', InvoiceSetting::class);

        $validated = $request->validated();

        $invoiceSetting = InvoiceSetting::create($validated);

        return new InvoiceSettingResource($invoiceSetting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InvoiceSetting $invoiceSetting
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, InvoiceSetting $invoiceSetting)
    {
        // $this->authorize('view', $invoiceSetting);

        return new InvoiceSettingResource($invoiceSetting);
    }

    /**
     * @param \App\Http\Requests\InvoiceSettingUpdateRequest $request
     * @param \App\Models\InvoiceSetting $invoiceSetting
     * @return \Illuminate\Http\Response
     */
    public function update(
        InvoiceSettingUpdateRequest $request,
        InvoiceSetting $invoiceSetting
    ) {
        // $this->authorize('update', $invoiceSetting);

        $validated = $request->validated();

        $invoiceSetting->update($validated);

        return new InvoiceSettingResource($invoiceSetting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InvoiceSetting $invoiceSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, InvoiceSetting $invoiceSetting)
    {
        //$this->authorize('delete', $invoiceSetting);

        $invoiceSetting->delete();

        return response()->noContent();
    }
}
