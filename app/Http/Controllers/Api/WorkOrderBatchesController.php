<?php

namespace App\Http\Controllers\Api;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BatchResource;
use App\Http\Resources\BatchCollection;

class WorkOrderBatchesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkOrder $workOrder
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, WorkOrder $workOrder)
    {
        // $this->authorize('view', $workOrder);

        $batches = $workOrder
            ->batches()
            ->latest()
            ->paginate();

        return new BatchCollection($batches);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkOrder $workOrder
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, WorkOrder $workOrder)
    {
        //$this->authorize('create', Batch::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'deadline' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'initial_qty' => ['required', 'numeric'],
            'doc_num' => ['nullable', 'max:255', 'string'],
            'doc' => ['nullable', 'max:255', 'string'],
        ]);

        $batch = $workOrder->batches()->create($validated);

        return new BatchResource($batch);
    }
}
