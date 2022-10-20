<?php

namespace App\Http\Controllers\Api;

use App\Models\Workplace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkplaceResource;
use App\Http\Resources\WorkplaceCollection;
use App\Http\Requests\WorkplaceStoreRequest;
use App\Http\Requests\WorkplaceUpdateRequest;

class WorkplaceController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Workplace::class);

        $search = $request->get('search', '');

        $workplaces = Workplace::search($search)
            ->latest()
            ->paginate();

        return new WorkplaceCollection($workplaces);
    }

    /**
     * @param \App\Http\Requests\WorkplaceStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkplaceStoreRequest $request)
    {
        $this->authorize('create', Workplace::class);

        $validated = $request->validated();

        $workplace = Workplace::create($validated);

        return new WorkplaceResource($workplace);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Workplace $workplace
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Workplace $workplace)
    {
        $this->authorize('view', $workplace);

        return new WorkplaceResource($workplace);
    }

    /**
     * @param \App\Http\Requests\WorkplaceUpdateRequest $request
     * @param \App\Models\Workplace $workplace
     * @return \Illuminate\Http\Response
     */
    public function update(
        WorkplaceUpdateRequest $request,
        Workplace $workplace
    ) {
        $this->authorize('update', $workplace);

        $validated = $request->validated();

        $workplace->update($validated);

        return new WorkplaceResource($workplace);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Workplace $workplace
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Workplace $workplace)
    {
        $this->authorize('delete', $workplace);

        $workplace->delete();

        return response()->noContent();
    }
}
