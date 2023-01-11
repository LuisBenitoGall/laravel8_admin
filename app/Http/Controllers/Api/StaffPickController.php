<?php

namespace App\Http\Controllers\Api;

use App\Models\StaffPick;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffPickResource;
use App\Http\Resources\StaffPickCollection;
use App\Http\Requests\StaffPickStoreRequest;
use App\Http\Requests\StaffPickUpdateRequest;
use Illuminate\Http\Response;

class StaffPickController extends Controller
{
    /**
     * @param Request $request
     * @return StaffPickCollection
     */
    public function index(Request $request)
    {


        $staffPicks = StaffPick::latest()->paginate();

        return new StaffPickCollection($staffPicks);
    }

    /**
     * @param \App\Http\Requests\StaffPickStoreRequest $request
     * @return Response
     */
    public function store(StaffPickStoreRequest $request)
    {


        $validated = $request->validated();

        $staffPick = StaffPick::create($validated);

        return new StaffPickResource($staffPick);
    }

    /**
     * @param Request $request
     * @param \App\Models\StaffPick $staffPick
     * @return Response
     */
    public function show(Request $request, StaffPick $staffPick)
    {


        return new StaffPickResource($staffPick);
    }

    /**
     * @param \App\Http\Requests\StaffPickUpdateRequest $request
     * @param \App\Models\StaffPick $staffPick
     * @return Response
     */
    public function update(
        StaffPickUpdateRequest $request,
        StaffPick              $staffPick
    )
    {


        $validated = $request->validated();

        $staffPick->update($validated);

        return new StaffPickResource($staffPick);
    }

    /**
     * @param Request $request
     * @param \App\Models\StaffPick $staffPick
     * @return Response
     */
    public function destroy(Request $request, StaffPick $staffPick)
    {

        $staffPick->delete();

        return response()->noContent();
    }
}
