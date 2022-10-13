<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Delivery;
use App\Models\DeliveryItems;

class DeliveryItemsController extends Controller{
    /**
     * 1. Guardar línea recibida.
     * 2. Eliminar línea recibida.
     */
    
    private $option = 'logistica';
    private $module = 'logistics';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:logistics');
    }  

    /**
     * 1. Guardar línea recibida.
     */
    public function store(Request $request){
        if($request->ajax()){ 
            $row = new DeliveryItems();
            $row->delivery_id = $request->delivery_id;
            $row->item_id = $request->id;
            $row->qty = $request->qty;
            $row->user_id = Auth::user()->id;
            $row->save();

            return response()->json($row);
        }
    }

    /**
     * 2. Eliminar línea recibida.
     */
    public function destroy(Delivery $delivery, $item_id){

        dd($delivery, $item_id);


        $msg = trans('textos.recepcion_linea_eliminada');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));    
    }
}
