<?php

namespace App\Http\Controllers\Api;

use App\Models\BatchPattern;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BatchPatternResource;
use App\Http\Resources\BatchPatternCollection;
use App\Http\Requests\BatchPatternStoreRequest;
use App\Http\Requests\BatchPatternUpdateRequest;

class BatchPatternController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', BatchPattern::class);

        //$search = $request->get('search', '');

        $batchPatterns = BatchPattern::latest()->paginate();

        return new BatchPatternCollection($batchPatterns);
    }

    /**
     * @param \App\Http\Requests\BatchPatternStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BatchPatternStoreRequest $request)
    {
        // $this->authorize('create', BatchPattern::class);

        $validated = $request->validated();

        $batchPattern = BatchPattern::create($validated);

        return new BatchPatternResource($batchPattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BatchPattern $batchPattern
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, BatchPattern $batchPattern)
    {
        // $this->authorize('view', $batchPattern);

        return new BatchPatternResource($batchPattern);
    }

    /**
     * @param \App\Http\Requests\BatchPatternUpdateRequest $request
     * @param \App\Models\BatchPattern $batchPattern
     * @return \Illuminate\Http\Response
     */
    public function update(
        BatchPatternUpdateRequest $request,
        BatchPattern $batchPattern
    ) {
        // $this->authorize('update', $batchPattern);

        $validated = $request->validated();

        $batchPattern->update($validated);

        return new BatchPatternResource($batchPattern);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BatchPattern $batchPattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, BatchPattern $batchPattern)
    {
        //$this->authorize('delete', $batchPattern);

        $batchPattern->delete();

        return response()->noContent();
    }
}
