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
use App\Models\Order;
use App\Models\OrderDocs;

class OrderDocsController extends Controller{
    /**
     * 1. Guardar documentos del pedido.
     * 2. Eliminar documento.
     */
    
    public function __construct(){
        $this->middleware('module_setted:orders');
        $this->middleware('order_get', ['only' => ['store']]);
    }   

    /**
     * 1. Guardar documentos del pedido.
     *
     * @param $request, $order
     */
    public function store(Request $request, $order){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                $file = $request->file('file');
                $path = public_path().'/img/orders';
                $fileName = uniqid().$file->getClientOriginalName();
                $moved = $file->move($path, $fileName);
                $type = $file->getClientMimeType();

                //si el archivo ha sido guardado correctamente:
                if($moved){
                    $doc = new OrderDocs;
                    $doc->doc = $fileName;
                    $doc->order_id = $order;
                    $doc->type = $type;
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
    public function destroy(OrderDocs $doc){
        if($doc != ''){
            File::delete(public_path().'/img/orders/'.$doc->doc);

            $doc->delete();

            $msg = trans('textos.archivo_deleted');
            return redirect()->route('orders.edit', [$doc->order_id, 'docs'])->with(compact('msg')); 
        
        }else{
            return redirect('/');
        } 
    }
}
