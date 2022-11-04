<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WorkOrderPatterns as WorkOrderPattern;;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkOrderPatternResource;
use App\Http\Resources\WorkOrderPatternCollection;
use App\Http\Requests\WorkOrderPatternStoreRequest;
use App\Http\Requests\WorkOrderPatternUpdateRequest;

class WorkOrderPatternController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', WorkOrderPattern::class);

        //$search = $request->get('search', '');

        $workOrderPatterns = WorkOrderPattern::latest()->paginate();

        return new WorkOrderPatternCollection($workOrderPatterns);
    }

    /**
     * @param \App\Http\Requests\WorkOrderPatternStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkOrderPatternStoreRequest $request)
    {
        // $this->authorize('create', WorkOrderPattern::class);

        $validated = $request->validated();

        $workOrderPattern = WorkOrderPattern::create($validated);

        return new WorkOrderPatternResource($workOrderPattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkOrderPattern $workOrderPattern
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, WorkOrderPattern $workOrderPattern)
    {
        // $this->authorize('view', $workOrderPattern);

        return new WorkOrderPatternResource($workOrderPattern);
    }

    /**
     * @param \App\Http\Requests\WorkOrderPatternUpdateRequest $request
     * @param \App\Models\WorkOrderPattern $workOrderPattern
     * @return \Illuminate\Http\Response
     */
    public function update(
        WorkOrderPatternUpdateRequest $request,
        WorkOrderPattern $workOrderPattern
    ) {
        // $this->authorize('update', $workOrderPattern);

        $validated = $request->validated();

        $workOrderPattern->update($validated);

        return new WorkOrderPatternResource($workOrderPattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WorkOrderPattern $workOrderPattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        Request $request,
        WorkOrderPattern $workOrderPattern
    ) {
        //$this->authorize('delete', $workOrderPattern);

        $workOrderPattern->delete();

        return response()->noContent();
    }
}
