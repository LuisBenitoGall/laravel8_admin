<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Models:
use App\Models\Company;

//Resources:
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

/**
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST, 
 *     description="Servidor local"
 * )
 *
 * @OA\Tag(
 *     name="Productos",
 *     description="Endpoints para gestionar productos de una compañía"
 * )
 */
class CompanyProductsController extends Controller{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    
     /**
     * @OA\Get(
     *     path="/companies/{company}/products",
     *     summary="Obtener lista de productos por empresa",
     *     tags={"Productos"},
     *
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         description="ID de la compañía de la cual se quieren listar los productos",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Listado de productos devuelto correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Producto")
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="Compañía no encontrada")
     * )
     */
    public function index(Request $request, Company $company){
        // $this->authorize('view', $company);

        // $products = $company
        //     ->products()
        //     ->latest()
        //     ->paginate();

        $products = $company
            ->products()
            ->orderBy('name', 'ASC')
            ->get();

        return new ProductCollection($products);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    
    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Crear un nuevo producto",
     *     tags={"Productos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductoCreateRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Producto")
     *     )
     * )
     */
    public function store(Request $request, Company $company){
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
