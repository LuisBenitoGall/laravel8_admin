<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\InvoicePattern;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoicePatternResource;
use App\Http\Resources\InvoicePatternCollection;
use App\Http\Requests\InvoicePatternStoreRequest;
use App\Http\Requests\InvoicePatternUpdateRequest;

class InvoicePatternController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', InvoicePattern::class);

        //$search = $request->get('search', '');

        $invoicePatterns = InvoicePattern::latest()->paginate();

        return new InvoicePatternCollection($invoicePatterns);
    }

    /**
     * @param \App\Http\Requests\InvoicePatternStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoicePatternStoreRequest $request)
    {
        // $this->authorize('create', InvoicePattern::class);

        $validated = $request->validated();

        $invoicePattern = InvoicePattern::create($validated);

        return new InvoicePatternResource($invoicePattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InvoicePattern $invoicePattern
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, InvoicePattern $invoicePattern)
    {
        // $this->authorize('view', $invoicePattern);

        return new InvoicePatternResource($invoicePattern);
    }

    /**
     * @param \App\Http\Requests\InvoicePatternUpdateRequest $request
     * @param \App\Models\InvoicePattern $invoicePattern
     * @return \Illuminate\Http\Response
     */
    public function update(
        InvoicePatternUpdateRequest $request,
        InvoicePattern $invoicePattern
    ) {
        // $this->authorize('update', $invoicePattern);

        $validated = $request->validated();

        $invoicePattern->update($validated);

        return new InvoicePatternResource($invoicePattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InvoicePattern $invoicePattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, InvoicePattern $invoicePattern)
    {
        //$this->authorize('delete', $invoicePattern);

        $invoicePattern->delete();

        return response()->noContent();
    }
}
