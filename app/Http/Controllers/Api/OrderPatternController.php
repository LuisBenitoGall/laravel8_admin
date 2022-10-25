<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderPattern;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderPatternResource;
use App\Http\Resources\OrderPatternCollection;
use App\Http\Requests\OrderPatternStoreRequest;
use App\Http\Requests\OrderPatternUpdateRequest;

class OrderPatternController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', OrderPattern::class);

        $search = $request->get('search', '');

        $orderPatterns = OrderPattern::search($search)
            ->latest()
            ->paginate();

        return new OrderPatternCollection($orderPatterns);
    }

    /**
     * @param \App\Http\Requests\OrderPatternStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderPatternStoreRequest $request)
    {
        // $this->authorize('create', OrderPattern::class);

        $validated = $request->validated();

        $orderPattern = OrderPattern::create($validated);

        return new OrderPatternResource($orderPattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\OrderPattern $orderPattern
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, OrderPattern $orderPattern)
    {
        // $this->authorize('view', $orderPattern);

        return new OrderPatternResource($orderPattern);
    }

    /**
     * @param \App\Http\Requests\OrderPatternUpdateRequest $request
     * @param \App\Models\OrderPattern $orderPattern
     * @return \Illuminate\Http\Response
     */
    public function update(
        OrderPatternUpdateRequest $request,
        OrderPattern $orderPattern
    ) {
        // $this->authorize('update', $orderPattern);

        $validated = $request->validated();

        $orderPattern->update($validated);

        return new OrderPatternResource($orderPattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\OrderPattern $orderPattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, OrderPattern $orderPattern)
    {
        //$this->authorize('delete', $orderPattern);

        $orderPattern->delete();

        return response()->noContent();
    }
}
