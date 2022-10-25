<?php

namespace App\Http\Controllers\Api;

use App\Models\CostCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CostCenterResource;
use App\Http\Resources\CostCenterCollection;
use App\Http\Requests\CostCenterStoreRequest;
use App\Http\Requests\CostCenterUpdateRequest;

class CostCenterController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', CostCenter::class);

        $search = $request->get('search', '');

        $costCenters = CostCenter::search($search)
            ->latest()
            ->paginate();

        return new CostCenterCollection($costCenters);
    }

    /**
     * @param \App\Http\Requests\CostCenterStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CostCenterStoreRequest $request)
    {
        // $this->authorize('create', CostCenter::class);

        $validated = $request->validated();

        $costCenter = CostCenter::create($validated);

        return new CostCenterResource($costCenter);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CostCenter $costCenter
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, CostCenter $costCenter)
    {
        // $this->authorize('view', $costCenter);

        return new CostCenterResource($costCenter);
    }

    /**
     * @param \App\Http\Requests\CostCenterUpdateRequest $request
     * @param \App\Models\CostCenter $costCenter
     * @return \Illuminate\Http\Response
     */
    public function update(
        CostCenterUpdateRequest $request,
        CostCenter $costCenter
    ) {
        // $this->authorize('update', $costCenter);

        $validated = $request->validated();

        $costCenter->update($validated);

        return new CostCenterResource($costCenter);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CostCenter $costCenter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, CostCenter $costCenter)
    {
        //$this->authorize('delete', $costCenter);

        $costCenter->delete();

        return response()->noContent();
    }
}
