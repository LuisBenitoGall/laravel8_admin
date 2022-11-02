<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

class CompanyProductsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $products = $company
            ->products()
            ->latest()
            ->paginate();

        return new ProductCollection($products);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'ref' => ['nullable', 'max:255', 'string'],
            'manual_ref' => ['nullable', 'max:255', 'string'],
            'translations' => ['nullable', 'max:255', 'string'],
            'tags' => ['nullable', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'long_description' => ['nullable', 'max:255', 'string'],
            'type' => ['nullable', 'max:1', 'string'],
            'price' => ['required', 'numeric'],
            'calculated_by_percent' => ['nullable', 'numeric'],
            'iva' => ['nullable', 'numeric'],
            'batch' => ['required', 'boolean'],
            'stock_management' => ['required', 'boolean'],
            'on_sale' => ['required', 'boolean'],
            'status' => ['required', 'numeric'],
            'user_id' => ['required', 'exists:users,id'],
            'product_pattern_id' => ['required', 'exists:product_patterns,id'],
        ]);

        $product = $company->products()->create($validated);

        return new ProductResource($product);
    }
}
