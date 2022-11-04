<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PurchasePatterns as PurchasePattern;
use App\Http\Controllers\Controller;
use App\Http\Resources\PurchasePatternResource;
use App\Http\Resources\PurchasePatternCollection;
use App\Http\Requests\PurchasePatternStoreRequest;
use App\Http\Requests\PurchasePatternUpdateRequest;

class PurchasePatternController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', PurchasePattern::class);

        //$search = $request->get('search', '');

        $purchasePatterns = PurchasePattern::latest()->paginate();

        return new PurchasePatternCollection($purchasePatterns);
    }

    /**
     * @param \App\Http\Requests\PurchasePatternStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchasePatternStoreRequest $request)
    {
        // $this->authorize('create', PurchasePattern::class);

        $validated = $request->validated();

        $purchasePattern = PurchasePattern::create($validated);

        return new PurchasePatternResource($purchasePattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PurchasePattern $purchasePattern
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, PurchasePattern $purchasePattern)
    {
        // $this->authorize('view', $purchasePattern);

        return new PurchasePatternResource($purchasePattern);
    }

    /**
     * @param \App\Http\Requests\PurchasePatternUpdateRequest $request
     * @param \App\Models\PurchasePattern $purchasePattern
     * @return \Illuminate\Http\Response
     */
    public function update(
        PurchasePatternUpdateRequest $request,
        PurchasePattern $purchasePattern
    ) {
        // $this->authorize('update', $purchasePattern);

        $validated = $request->validated();

        $purchasePattern->update($validated);

        return new PurchasePatternResource($purchasePattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PurchasePattern $purchasePattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PurchasePattern $purchasePattern)
    {
        //$this->authorize('delete', $purchasePattern);

        $purchasePattern->delete();

        return response()->noContent();
    }
}
