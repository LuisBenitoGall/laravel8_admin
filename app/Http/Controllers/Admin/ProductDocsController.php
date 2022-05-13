<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\Product;
use App\Models\ProductDocs;

class ProductDocsController extends Controller{
    /**
     * 1. Guardar documentos del producto.
     * 2. Eliminar documento.
     * 3. Destacar imagen.
     */
    
    public function __construct(){
        $this->middleware('module_setted:products');
        $this->middleware('product_get', ['only' => ['store']]);
    }

    /**
     * 1. Guardar documentos del producto.
     *
     * @param $request, $product
     */
    public function store(Request $request, $product){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                $file = $request->file('file');
                $path = public_path().'/img/products';
                $fileName = uniqid().$file->getClientOriginalName();
                $moved = $file->move($path, $fileName);
                $type = $file->getClientMimeType();

                //Comprobamos si existe imagen destacada:
                $row = ProductDocs::select('id')
                ->where('product_id', $product)
                ->where('featured', 1)
                ->first();
                $featured = $row? false:true;

                //si el archivo ha sido guardado correctamente:
                if($moved){
                    $doc = new ProductDocs;
                    $doc->doc = $fileName;
                    $doc->product_id = $product;
                    $doc->type = $type;
                    $doc->featured = $featured;
                    $doc->save();
                }else{
                    return false;
                }
            }    
        }
    }   

    /**
     * 2. Eliminar documento.
     *
     * @param  obj $doc
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductDocs $doc){
        if($doc != ''){
            File::delete(public_path().'/img/products/'.$doc->doc);

            $doc->delete();

            $msg = trans('textos.archivo_deleted');
            return redirect()->route('products.edit', [$doc->product_id, 'docs'])->with(compact('msg')); 
        
        }else{
            return redirect('/');
        } 
    }

    /**
     * 3. Destacar imagen.
     *
     * @param  request $product $image
     */
    public function setFeatured(Request $request, $product, $image){
        if($request->ajax()){ 
            if($product && $image != ''){   
                ProductDocs::where('product_id', $product)
                ->update(['featured' => 0]);

                $img = ProductDocs::find($image);
                $img->featured = 1;
                $img->save();

            }else{
                return redirect('/');
            }
        }  
    }
}
