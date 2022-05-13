<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

//Models:
use App\Models\DeliveryOrders;

class DeliveryOrdersController extends Controller{
    /**
     * 1. Vincular pedido a entrega.
     * 2. Desvincular pedido de entrega.
     */
    
    private $option = 'entregas';
    private $module = 'logistics';

    public function __construct(){
        $this->middleware('module_setted:logistics');
        $this->middleware('delivery_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'delivery_id' => 'required|integer',
                'orders' => 'required'
            ]);
        }
    }

    /**
     * 1. Vincular pedido a entrega.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/deliveries/'.$request->delivery_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        foreach($request->orders as $order){
            $do = new DeliveryOrders();
            $do->delivery_id = $request->delivery_id;
            $do->order_id = $order;
            $do->save();
        }

        $msg = trans('textos.pedidos_vinculados_entrega');

        if($request->return == 'delivery'){
            return redirect()->route('deliveries.edit', [$request->delivery_id, 'orders'])->with(compact('msg'));
        }else{
            
        }
    }

    /**
     * 2. Desvincular pedido de entrega.
     */
    public function destroy(DeliveryOrders $delivery_order){
        $delivery_id = $delivery_order->delivery_id; 

        $delivery_order->delete();

        $msg = trans('textos.pedido_desvinculado');
        return redirect()->route('deliveries.edit', [$delivery_id, 'orders'])->with(compact('msg'));
    }
}
