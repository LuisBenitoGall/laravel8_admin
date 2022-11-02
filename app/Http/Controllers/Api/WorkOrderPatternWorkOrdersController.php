<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WorkOrderPatterns as WorkOrderPattern;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkOrderResource;
use App\Http\Resources\WorkOrderCollection;

class WorkOrderPatternWorkOrdersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkOrderPattern $workOrderPattern
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, WorkOrderPattern $workOrderPattern)
    {
        // $this->authorize('view', $workOrderPattern);

        $workOrders = $workOrderPattern
            ->workOrders()
            ->latest()
            ->paginate();

        return new WorkOrderCollection($workOrders);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkOrderPattern $workOrderPattern
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, WorkOrderPattern $workOrderPattern)
    {
        //$this->authorize('create', WorkOrder::class);

        $validated = $request->validate([
            'work_order_num' => ['nullable', 'max:255', 'string'],
            'num_cardinal' => ['nullable', 'max:255', 'string'],
            'work_order_old_num' => ['nullable', 'max:255', 'string'],
            'name' => ['nullable', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'estimated_delivery_date' => ['nullable', 'date'],
            'delivery_date' => ['nullable', 'date'],
            'simulation' => ['required', 'boolean'],
            'status' => ['required', 'numeric'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'updated_by' => ['nullable', 'exists:users,id'],
        ]);

        $workOrder = $workOrderPattern->workOrders()->create($validated);

        return new WorkOrderResource($workOrder);
    }
}
