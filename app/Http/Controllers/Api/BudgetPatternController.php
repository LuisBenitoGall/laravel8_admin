<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\BudgetPatterns as BudgetPattern;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetPatternResource;
use App\Http\Resources\BudgetPatternCollection;
use App\Http\Requests\BudgetPatternStoreRequest;
use App\Http\Requests\BudgetPatternUpdateRequest;

class BudgetPatternController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', BudgetPattern::class);

        //$search = $request->get('search', '');

        $budgetPatterns = BudgetPattern::latest()->paginate();

        return new BudgetPatternCollection($budgetPatterns);
    }

    /**
     * @param \App\Http\Requests\BudgetPatternStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BudgetPatternStoreRequest $request)
    {
        // $this->authorize('create', BudgetPattern::class);

        $validated = $request->validated();

        $budgetPattern = BudgetPattern::create($validated);

        return new BudgetPatternResource($budgetPattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BudgetPattern $budgetPattern
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, BudgetPattern $budgetPattern)
    {
        // $this->authorize('view', $budgetPattern);

        return new BudgetPatternResource($budgetPattern);
    }

    /**
     * @param \App\Http\Requests\BudgetPatternUpdateRequest $request
     * @param \App\Models\BudgetPattern $budgetPattern
     * @return \Illuminate\Http\Response
     */
    public function update(
        BudgetPatternUpdateRequest $request,
        BudgetPattern $budgetPattern
    ) {
        // $this->authorize('update', $budgetPattern);

        $validated = $request->validated();

        $budgetPattern->update($validated);

        return new BudgetPatternResource($budgetPattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BudgetPattern $budgetPattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, BudgetPattern $budgetPattern)
    {
        //$this->authorize('delete', $budgetPattern);

        $budgetPattern->delete();

        return response()->noContent();
    }
}
