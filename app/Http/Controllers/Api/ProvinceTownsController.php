<?php

namespace App\Http\Controllers\Api;

use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Resources\TownResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\TownCollection;

class ProvinceTownsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Province $province
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Province $province)
    {
        // $this->authorize('view', $province);

        $search = $request->get('search', '');

        $towns = $province
            ->towns()
            ->search($search)
            ->latest()
            ->paginate();

        return new TownCollection($towns);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Province $province
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Province $province)
    {
        //$this->authorize('create', Town::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'status' => ['nullable', 'numeric'],
        ]);

        $town = $province->towns()->create($validated);

        return new TownResource($town);
    }
}
