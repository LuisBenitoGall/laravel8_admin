<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProvinceResource;
use App\Http\Resources\ProvinceCollection;

class CountryProvincesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Country $country
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Country $country)
    {
        // $this->authorize('view', $country);

        $provinces = $country
            ->provinces()
            ->latest()
            ->paginate();

        return new ProvinceCollection($provinces);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Country $country
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Country $country)
    {
        //$this->authorize('create', Province::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'status' => ['required', 'numeric'],
        ]);

        $province = $country->provinces()->create($validated);

        return new ProvinceResource($province);
    }
}
