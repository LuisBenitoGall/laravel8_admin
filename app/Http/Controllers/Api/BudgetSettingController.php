<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\BudgetSetting;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetSettingResource;
use App\Http\Resources\BudgetSettingCollection;
use App\Http\Requests\BudgetSettingStoreRequest;
use App\Http\Requests\BudgetSettingUpdateRequest;

class BudgetSettingController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', BudgetSetting::class);

        $search = $request->get('search', '');

        $budgetSettings = BudgetSetting::search($search)
            ->latest()
            ->paginate();

        return new BudgetSettingCollection($budgetSettings);
    }

    /**
     * @param \App\Http\Requests\BudgetSettingStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BudgetSettingStoreRequest $request)
    {
        // $this->authorize('create', BudgetSetting::class);

        $validated = $request->validated();

        $budgetSetting = BudgetSetting::create($validated);

        return new BudgetSettingResource($budgetSetting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BudgetSetting $budgetSetting
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, BudgetSetting $budgetSetting)
    {
        // $this->authorize('view', $budgetSetting);

        return new BudgetSettingResource($budgetSetting);
    }

    /**
     * @param \App\Http\Requests\BudgetSettingUpdateRequest $request
     * @param \App\Models\BudgetSetting $budgetSetting
     * @return \Illuminate\Http\Response
     */
    public function update(
        BudgetSettingUpdateRequest $request,
        BudgetSetting $budgetSetting
    ) {
        // $this->authorize('update', $budgetSetting);

        $validated = $request->validated();

        $budgetSetting->update($validated);

        return new BudgetSettingResource($budgetSetting);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BudgetSetting $budgetSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, BudgetSetting $budgetSetting)
    {
        //$this->authorize('delete', $budgetSetting);

        $budgetSetting->delete();

        return response()->noContent();
    }
}
