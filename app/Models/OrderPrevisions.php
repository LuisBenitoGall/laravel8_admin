<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OrderPrevisions extends Model{
    /**
     * 1. Guardar previsión de facturación.
     * 2. Recalcular porcentajes de previsiones de facturación.
     */

    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $table = 'order_previsions'; 

    /**
     * 1. Guardar previsión de facturación.
     */
    public static function store($order_id, $year = false, $percent = 100){
        $op = new OrderPrevisions();
        $op->order_id = $order_id;
        $op->year = $year? $year:date('Y');
        $op->percent = $percent;
        $op->save();  
        
        return $op;  
    } 

    /**
     * 2. Recalcular porcentajes de previsiones de facturación.
     */
    public static function recalculatePrevisionsPercent($order_id){
        $shared = BudgetOrderSharedFields::where('order_id', $order_id)->first();

        $previsions = OrderPrevisions::select('order_previsions.id', 'order_previsions.month', 'order_previsions.percent', 'invoices.amount')
        ->leftJoin('invoices', 'order_previsions.invoice_id', '=', 'invoices.id')
        ->where('order_previsions.order_id', $order_id)
        ->get();

        //Importe del pedido:
        if($shared->free_amount){
            $order_amount = $shared->free_amount;
        }else{
            $arr_amounts = $shared->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
        } 

        $total_percent = 0;

        foreach($previsions as $prev){
            if($prev->amount){
                $percent = ($prev->amount * 100) / $order_amount;

                //Si se supera el 100%, se ajusta la última previsión:
                $total_percent = $total_percent + round($percent, 2);
                if($total_percent > 100){
                    $diff = $total_percent - 100;
                    $percent = round($percent - $diff, 2);
                }

                $prev->month = $prev->month? $prev->month:Date('m');
                $prev->percent = round($percent, 2);
                $prev->save();
            }
        }
    }

}
