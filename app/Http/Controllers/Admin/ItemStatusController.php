<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\ItemStatus;

class ItemStatusController extends Controller{
    /**
     * 1. Rondas de cambios por línea de pedido.
     * 2. Guardar ronda de cambios.
     * 3. Eliminar ronda de cambios.
     * 4. Estado de ronda de cambios.
     */
    
    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:orders');
    }

    /**
     * 1. Rondas de cambios por línea de pedido.
     */
    public function index(Request $request, $id = false){
        if($request->ajax() && $id){ 
            $data = ItemStatus::where('item_id', $id)
            ->orderBy('id', 'ASC')
            ->get();

            return response()->json($data);
        }
    }

    /**
     * 2. Guardar ronda de cambios.
     */
    public function store(Request $request){
        $item = new ItemStatus();
        $item->item_id = $request->item_id;
        $item->provider_observations = $request->provider_observations;
        $item->customer_observations = $request->customer_observations;
        $item->save();

        $msg = trans('textos.ronda_cambios_guardada');
        return redirect()->route('orders.edit', [$request->order_id, 'items'])->with(compact('msg'));
    }

    /**
     * 3. Eliminar ronda de cambios.
     */
    public function destroy($id = false){
        if($id){
            //Obtener order_id para el retorno:
            $order = ItemStatus::select('items.order_id')
            ->join('items', 'item_status.item_id', '=', 'items.id') 
            ->where('item_status.id', $id)
            ->first();

            //Eliminamos:
            ItemStatus::where('id', $id)->delete();

            $msg = trans('textos.ronda_cambios_deleted');
            return redirect()->route('orders.edit', [$order->order_id, 'lines'])->with(compact('msg'));

        }else{
            return redirect('/');   
        }
    }

    /**
     * 4. Estado de ronda de cambios.
     */
    public function status(Request $request, ItemStatus $item, $status){
        if($request->ajax()){   
            $item->status = $status;
            $item->save();

            $resp = ($item)? true:false;
            return response()->json($resp);
        }
    }   
}
