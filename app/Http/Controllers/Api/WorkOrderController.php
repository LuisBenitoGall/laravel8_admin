<?php

namespace App\Http\Controllers\Api;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkOrderResource;
use App\Http\Resources\WorkOrderCollection;
use App\Http\Requests\WorkOrderStoreRequest;
use App\Http\Requests\WorkOrderUpdateRequest;

class WorkOrderController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', WorkOrder::class);

        //$search = $request->get('search', '');

        $workOrders = WorkOrder::latest()->paginate();

        return new WorkOrderCollection($workOrders);
    }

    /**
     * @param \App\Http\Requests\WorkOrderStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkOrderStoreRequest $request)
    {
        // $this->authorize('create', WorkOrder::class);

        $validated = $request->validated();

        $workOrder = WorkOrder::create($validated);

        return new WorkOrderResource($workOrder);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkOrder $workOrder
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, WorkOrder $workOrder)
    {
        // $this->authorize('view', $workOrder);

        return new WorkOrderResource($workOrder);
    }

    /**
     * @param \App\Http\Requests\WorkOrderUpdateRequest $request
     * @param \App\Models\WorkOrder $workOrder
     * @return \Illuminate\Http\Response
     */
    public function update(
        WorkOrderUpdateRequest $request,
        WorkOrder $workOrder
    ) {
        // $this->authorize('update', $workOrder);

        $validated = $request->validated();

        $workOrder->update($validated);

        return new WorkOrderResource($workOrder);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkOrder $workOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, WorkOrder $workOrder)
    {
        //$this->authorize('delete', $workOrder);

        $workOrder->delete();

        return response()->noContent();
    }
}
