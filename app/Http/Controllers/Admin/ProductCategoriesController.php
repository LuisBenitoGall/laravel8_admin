<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Category;
use App\Models\ProductCategories;

class ProductCategoriesController extends Controller{
    /**
     * 1. Relación de categorías por producto.
     * 2. Guardar / eliminar categoría por producto.
     */

    private $option = 'catalogo';  
    private $module = 'products';

    public function __construct(){
        $this->middleware('module_setted:products');
    }

    /**
     * 1. Relación de categorías por producto.
     */
    public function getCategoriesDT($product_id){
        $data = Category::select('categories.*')
        ->join('modules', 'categories.module_id', '=', 'modules.id')
        ->where('categories.status', 1)
        ->where('categories.company_id', session('currentCompany'))
        ->where('modules.slug', 'products')
        ->with('sub_category')
        ->orderBy('name', 'ASC');

        //Categorías del producto:
        $categories = ProductCategories::where('product_id', $product_id)
        ->pluck('category_id')
        ->toArray();

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return ucfirst($row->name);
        })
        ->addColumn('level', function($row){
            $level = trans('textos.categoria_principal');
            if($row->subcategory > 0){
                $x = $row->getCategoryName($row->subcategory);
                $level = trans('textos.subcategoria_de').' <b>'.ucfirst($x).'</b>';    
            }
            return $level;
        })
        ->addColumn('acciones', function($row) use($categories){
            return view('admin.product_categories.partials.dt-actions', compact('row', 'categories'));    
        })
        ->rawColumns(['level'])
        ->make(true);
    }

    /**
     * 2. Guardar / eliminar categoría por producto.
     */
    public function store(Request $request, $product_id, $category_id){
        if($request->ajax()){ 
            $exist = ProductCategories::select('id')
            ->where('product_id', $product_id)
            ->where('category_id', $category_id)
            ->first();

            if($exist){
                $exist->delete();
            }else{
                $c = new ProductCategories();
                $c->product_id = $product_id;
                $c->category_id = $category_id;
                $c->save();
            }

            return response()->json(true);
        }    
    }
}
