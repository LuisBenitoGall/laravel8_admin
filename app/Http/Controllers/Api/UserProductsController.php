<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

class UserProductsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        // $this->authorize('view', $user);

        $products = $user
            ->products()
            ->latest()
            ->paginate();

        return new ProductCollection($products);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
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
            'product_pattern_id' => ['required', 'exists:product_patterns,id'],
        ]);

        $product = $user->products()->create($validated);

        return new ProductResource($product);
    }
}
