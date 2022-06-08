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
        $shared = BudgetOrderSharedFields::where('order_id', $order->id)->first();

        $previsions = OrderPrevisions::select('order_previsions.id', 'order_previsions.percent', 'invoices.amount')
        ->leftJoin('invoices', 'order_previsions.invoice_id', '=', 'invoices.id')
        ->where('order_previsions.order_id', $order->id)
        ->get();

        //Importe del pedido:
        if($shared->free_amount){
            $order_amount = $shared->free_amount;
        }else{
            $arr_amounts = $shared->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
        } 

        foreach($previsions as $prev){
            if($prev->amount){
                $percent = ($prev->amount * 100) / $order_amount;
                $prev->percent = round($percent, 2);
                $prev->save();
            }
        }

        $msg = trans('textos.previsiones_recalculadas');
        return redirect()->route('orders.edit', [$order->id, false])->with(compact('msg')); 
    }

}
