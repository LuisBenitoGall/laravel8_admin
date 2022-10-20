<?php

namespace App\Http\Controllers\Api;

use App\Models\Town;
use Illuminate\Http\Request;
use App\Http\Resources\TownResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\TownCollection;
use App\Http\Requests\TownStoreRequest;
use App\Http\Requests\TownUpdateRequest;

class TownController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Town::class);

        $search = $request->get('search', '');

        $towns = Town::search($search)
            ->latest()
            ->paginate();

        return new TownCollection($towns);
    }

    /**
     * @param \App\Http\Requests\TownStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TownStoreRequest $request)
    {
        $this->authorize('create', Town::class);

        $validated = $request->validated();

        $town = Town::create($validated);

        return new TownResource($town);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Town $town
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Town $town)
    {
        $this->authorize('view', $town);

        return new TownResource($town);
    }

    /**
     * @param \App\Http\Requests\TownUpdateRequest $request
     * @param \App\Models\Town $town
     * @return \Illuminate\Http\Response
     */
    public function update(TownUpdateRequest $request, Town $town)
    {
        $this->authorize('update', $town);

        $validated = $request->validated();

        $town->update($validated);

        return new TownResource($town);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Town $town
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Town $town)
    {
        $this->authorize('delete', $town);

        $town->delete();

        return response()->noContent();
    }
}
