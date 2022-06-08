<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\OrderGroupOrders;

class OrderGroupOrdersController extends Controller{
    /**
     * 1. Guardar pedidos en agrupación.
     * 2. Añadir pedido a agrupación.
     * 3. Comprobar si pedido ya pertenece a alguna agrupación.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';
    private $languages;   

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:orders');
        $this->middleware('order_groups_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'group_id' => 'required|integer',
                'orders' => 'required'
            ]);

        //Validación añadir pedido:
        }elseif(isset($data['submit_add_order'])){
            return Validator::make($data, [
                'order_id' => 'required|integer',
                'order_group' => 'required|integer'
            ]);
        }
    }

    /**
     * 1. Guardar pedidos en agrupación.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-orders');

            return redirect('/admin/order-groups/'.$request->group_id.'/edit')
                    ->withErrors($validator)
                    ->withInput();
        } 

        //05/06/22: quedan acciones que incluir tal y como están en el Admin 6, aunque debo valorar si van a ser necesarias o no.

        //Asociamos pedidos al grupo:
        foreach($request->orders as $o){
            //Comprobamos que el pedido no esté asociado previamente:
            $exist = $this->ifExist($o);

            if(!$exist){
                $ogo = new OrderGroupOrders();
                $ogo->og_id = $request->group_id;
                $ogo->order_id = $o;
                $ogo->user_id = Auth::user()->id;
                $ogo->save();    
            }
        }

        $msg = trans('textos.pedido_agregado');
        return redirect()->route('order-groups.edit', [$request->group_id, 'orders'])->with(compact('msg'));
    }

    /**
     * 2. Añadir pedido a agrupación.
     */
    public function addOrder(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-link-order-group');

            return redirect('/admin/orders/'.$request->order_id.'/edit')
                    ->withErrors($validator)
                    ->withInput();
        } 

        $exist = $this->ifExist($request->order_id);

        if(!$exist){
            $ogo = new OrderGroupOrders();
            $ogo->og_id = $request->order_group;
            $ogo->order_id = $request->order_id;
            $ogo->user_id = Auth::user()->id;
            $ogo->save();    
        }

        $msg = trans('textos.pedido_vinculado_ok');
        return redirect()->route('orders.edit', $request->order_id)->with(compact('msg'));
    }

    /**
     * 3. Comprobar si pedido ya pertenece a alguna agrupación.
     */
    public function ifExist($order_id){
        $data = OrderGroupOrders::select('id')
        ->where('order_id', $order_id)
        ->exists();  

        if($data){
            return true;
        }else{
            return false;
        }    
    }
}
