<?php

namespace App\Http\Controllers\Api;

use App\Models\BusinessAreas as BusinessArea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessAreaResource;
use App\Http\Resources\BusinessAreaCollection;
use App\Http\Requests\BusinessAreaStoreRequest;
use App\Http\Requests\BusinessAreaUpdateRequest;

class BusinessAreaController extends Controller
{
    /**
     * @return \App\Http\Resources\BusinessAreaCollection
     */
    public function index()
    {
        // $this->authorize('view-any', BusinessArea::class);

        //$search = $request->get('search', '');

        $businessAreas = BusinessArea::latest()->paginate();

        return new BusinessAreaCollection($businessAreas);
    }

    /**
     * @param \App\Http\Requests\BusinessAreaStoreRequest $request
     * @return \App\Http\Resources\BusinessAreaResource
     */
    public function store(BusinessAreaStoreRequest $request)
    {
        // $this->authorize('create', BusinessArea::class);

        $validated = $request->validated();

        $businessArea = BusinessArea::create($validated);

        return new BusinessAreaResource($businessArea);
    }

    /**
     * @param \App\Models\BusinessAreas $businessArea
     * @return \App\Http\Resources\BusinessAreaResource
     */
    public function show(BusinessArea $businessArea)
    {
        // $this->authorize('view', $businessArea);

        return new BusinessAreaResource($businessArea);
    }

    /**
     * @param \App\Http\Requests\BusinessAreaUpdateRequest $request
     * @param \App\Models\BusinessAreas $businessArea
     * @return \App\Http\Resources\BusinessAreaResource
     */
    public function update(
        BusinessAreaUpdateRequest $request,
        BusinessArea $businessArea
    ) {
        // $this->authorize('update', $businessArea);

        $validated = $request->validated();

        $businessArea->update($validated);

        return new BusinessAreaResource($businessArea);
    }

    /**
     * @param \App\Models\BusinessAreas $businessArea
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessArea $businessArea)
    {
        //$this->authorize('delete', $businessArea);

        $businessArea->delete();

        return response()->noContent();
    }
}
