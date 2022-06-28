<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\BudgetOrderSharedFields;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderPrevisions;

class OrderPrevisionsController extends Controller{
    /**
     * 1. Eliminar previsión de facturación.
     * 2. Recalcular previsiones de facturación.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';

    public function __construct(){
        $this->middleware('module_setted:orders');
        $this->middleware('order_get', ['only' => ['recalculatePrevisions']]);
    }

    /**
     * 1. Eliminar previsión de facturación.
     */
    public function destroy(OrderPrevisions $prevision){
        //Si la previsión no está facturada:
        if($prevision->invoiced == 1){
            $alert = trans('textos.prevision_eliminar_error');
            return redirect()->route('orders.edit', $order_id)->with(compact('alert'));
            exit;

        }else{
            $order_id = $prevision->order_id;
            $prevision->delete();

            $msg = trans('textos.prevision_deleted');
            return redirect()->route('orders.edit', $order_id)->with(compact('msg')); 
        }
    }

    /**
     * 2. Recalcular previsiones de facturación.
     */
    public function recalculatePrevisions(Order $order){
        OrderPrevisions::recalculatePrevisionsPercent($order->id);

        $msg = trans('textos.previsiones_recalculadas');
        return redirect()->route('orders.edit', [$order->id, false])->with(compact('msg')); 
    }

}
