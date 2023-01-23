<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;

//Models:
use App\Models\Batch;

class BatchController extends Controller{
    /**
     * 1. Lotes por producto.
     */
    
    private $option = 'catalogo';  
    private $module = 'products';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:products');
    }

    /**
     * 1. Lotes por producto.
     */
    public function productBatches(Request $request, $product_id){
        if($request->ajax()){
            $data = Batch::select('id', 'name')
            ->where('product_id', $product_id)
            ->groupBy('id')
            ->get();

            return response()->json($data);
        }
    }
}
