<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\AttributeCollection;

class CompanyAttributesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $attributes = $company
            ->attributes()
            ->latest()
            ->paginate();

        return new AttributeCollection($attributes);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company): \Illuminate\Http\Response
    {
        //$this->authorize('create', Attribute::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'datatype' => ['required', 'numeric'],
            'values' => ['nullable', 'max:255', 'string'],
            'status' => ['required', 'numeric'],
        ]);

        $attribute = $company->attributes()->create($validated);

        return new AttributeResource($attribute);
    }
}
