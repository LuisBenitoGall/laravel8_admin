<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\ItemLinkedOrder;

class ItemLinkedOrderController extends Controller{
    /**
     * 1. Guardar vínculo de item con pedido de terceros.
     * 2. Pedidos vinculados a item.
     * 3. Desvincular item de pedido.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:orders');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación store:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'order_id' => 'required|integer',
                'item_id' => 'required|integer'
            ]);
        }
    }

    /**
     * 1. Guardar vínculo de item con pedido de terceros.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-link-item');

            return redirect('admin/orders/'.$request->order_id.'/edit/items')
            ->withErrors($validator)
            ->withInput();    
        }

        $i = new ItemLinkedOrder();
        $i->item_id = $request->item_id;
        $i->order_id = $request->order_id;
        $i->created_by = Auth::user()->id;
        $i->save();

        $msg = trans('textos.item_vinculado_pedido');
        if($request->environment == 'orders'){
            return redirect()->route('orders.edit', [$request->order_origin, 'items'])->with(compact('msg'));
        }elseif($request->environment == 'invoices'){
            return redirect()->route('invoices.edit', [$request->invoice_id, 'items'])->with(compact('msg'));    
        }
    }

    /**
     * 2. Pedidos vinculados a item.
     */
    public function show(Request $request, $item_id){
        if($request->ajax()){
            $data = ItemLinkedOrder::select('item_linked_orders.id', 'item_linked_orders.created_at', 'orders.id AS order_id', 'orders.name', 'orders.order_num', 'orders.order_old_num')  
            ->join('orders', 'item_linked_orders.order_id', '=', 'orders.id')
            ->where('item_linked_orders.item_id', $item_id)
            ->orderBy('orders.name')
            ->get();

            return response()->json($data);
        }    
    }

    /**
     * 3. Desvincular item de pedido.
     */
    public function destroy(ItemLinkedOrder $item_linked, $order_id){
        $item_linked->delete();

        $msg = trans('textos.item_desvinculado');
        return redirect()->route('orders.edit', [$order_id, 'items'])->with(compact('msg'));
    }
}
