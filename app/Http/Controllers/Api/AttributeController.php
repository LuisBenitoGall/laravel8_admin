<?php

namespace App\Http\Controllers\Api;

use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\AttributeCollection;
use App\Http\Requests\AttributeStoreRequest;
use App\Http\Requests\AttributeUpdateRequest;

class AttributeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\AttributeCollection
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', Attribute::class);

        //$search = $request->get('search', '');

        $attributes = Attribute::latest()->paginate();

        return new AttributeCollection($attributes);
    }

    /**
     * @param \App\Http\Requests\AttributeStoreRequest $request
     * @return \App\Http\Resources\AttributeResource
     */
    public function store(AttributeStoreRequest $request)
    {
        // $this->authorize('create', Attribute::class);

        $validated = $request->validated();

        $attribute = Attribute::create($validated);

        return new AttributeResource($attribute);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attribute $attribute
     * @return \App\Http\Resources\AttributeResource
     */
    public function show(Request $request, Attribute $attribute)
    {
        // $this->authorize('view', $attribute);

        return new AttributeResource($attribute);
    }

    /**
     * @param \App\Http\Requests\AttributeUpdateRequest $request
     * @param \App\Models\Attribute $attribute
     * @return \App\Http\Resources\AttributeResource
     */
    public function update(
        AttributeUpdateRequest $request,
        Attribute $attribute
    ) {
        // $this->authorize('update', $attribute);

        $validated = $request->validated();

        $attribute->update($validated);

        return new AttributeResource($attribute);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Attribute $attribute)
    {
        //$this->authorize('delete', $attribute);

        $attribute->delete();

        return response()->noContent();
    }
}
