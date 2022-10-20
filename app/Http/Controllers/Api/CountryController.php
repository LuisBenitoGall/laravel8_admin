<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CountryCollection;
use App\Http\Requests\CountryStoreRequest;
use App\Http\Requests\CountryUpdateRequest;

class CountryController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Country::class);

        $search = $request->get('search', '');

        $countries = Country::search($search)
            ->latest()
            ->paginate();

        return new CountryCollection($countries);
    }

    /**
     * @param \App\Http\Requests\CountryStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryStoreRequest $request)
    {
        $this->authorize('create', Country::class);

        $validated = $request->validated();

        $country = Country::create($validated);

        return new CountryResource($country);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Country $country
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Country $country)
    {
        $this->authorize('view', $country);

        return new CountryResource($country);
    }

    /**
     * @param \App\Http\Requests\CountryUpdateRequest $request
     * @param \App\Models\Country $country
     * @return \Illuminate\Http\Response
     */
    public function update(CountryUpdateRequest $request, Country $country)
    {
        $this->authorize('update', $country);

        $validated = $request->validated();

        $country->update($validated);

        return new CountryResource($country);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Country $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Country $country)
    {
        $this->authorize('delete', $country);

        $country->delete();

        return response()->noContent();
    }
}
