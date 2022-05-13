<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//Models:
use App\Models\ProductFavorites;

class ProductFavoritesController extends Controller{
    /**
     * 1. Añadir - quitar favorito.
     */
    
    /**
     * 1. Añadir - quitar favorito.
     */
    public function add_remove(Request $request, $product_id){
        if($request->ajax()){
            $fav = ProductFavorites::where('product_id', $product_id)
            ->where('user_id', Auth::user()->id)
            ->first();

            if($fav){
                $fav->delete();
                $resp = 'delete';

            }else{
                $fav = new ProductFavorites();
                $fav->product_id = $product_id;
                $fav->user_id = Auth::user()->id;
                $fav->save();
                $resp = 'save';
            }
            return response()->json($resp);
        }
    }
}
