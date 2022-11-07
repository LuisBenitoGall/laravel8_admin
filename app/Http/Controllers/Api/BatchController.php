<?php

namespace App\Http\Controllers\Api;

use App\Models\Batch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BatchResource;
use App\Http\Resources\BatchCollection;
use App\Http\Requests\BatchStoreRequest;
use App\Http\Requests\BatchUpdateRequest;

class BatchController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', Batch::class);

        //$search = $request->get('search', '');

        $batches = Batch::latest()->paginate();

        return new BatchCollection($batches);
    }

    /**
     * @param \App\Http\Requests\BatchStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BatchStoreRequest $request)
    {
        // $this->authorize('create', Batch::class);

        $validated = $request->validated();

        $batch = Batch::create($validated);

        return new BatchResource($batch);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Batch $batch
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Batch $batch)
    {
        // $this->authorize('view', $batch);

        return new BatchResource($batch);
    }

    /**
     * @param \App\Http\Requests\BatchUpdateRequest $request
     * @param \App\Models\Batch $batch
     * @return \Illuminate\Http\Response
     */
    public function update(BatchUpdateRequest $request, Batch $batch)
    {
        // $this->authorize('update', $batch);

        $validated = $request->validated();

        $batch->update($validated);

        return new BatchResource($batch);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Batch $batch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Batch $batch)
    {
        //$this->authorize('delete', $batch);

        $batch->delete();

        return response()->noContent();
    }
}
