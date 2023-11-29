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
     * 3. Previsiones de facturación por pedido.
     */

    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $table = 'order_previsions'; 

    protected $fillable = ['order_id', 'year', 'month', 'percent'];

    /**
     * 1. Guardar previsión de facturación.
     */
    public static function store($order_id, $year = false, $percent = 100){
        // $op = new OrderPrevisions();
        // $op->order_id = $order_id;
        // $op->year = $year? $year:date('Y');
        // $op->percent = $percent;
        // $op->save();  

        if(!$year) $year = date('Y');

        $op = OrderPrevisions::firstOrCreate(
            ['order_id'=> $order_id],
            ['order_id' => $order_id, 'year' => $year, 'percent' => $percent]
        );
        
        return $op;  
    } 

    /**
     * 2. Recalcular porcentajes de previsiones de facturación.
     *
     * $all_invoiced: indica si el pedido se ha facturado por completo.
     */
    public static function recalculatePrevisionsPercent($order, $all_invoiced = false){
        $previsions = OrderPrevisions::select('order_previsions.id', 'order_previsions.month', 'order_previsions.percent', 'invoices.amount', 'order_previsions.invoice_id')
        ->leftJoin('invoices', 'order_previsions.invoice_id', '=', 'invoices.id')
        ->where('order_previsions.order_id', $order->id)
        ->get();

        //Importe del pedido:
        if($order->bag_amount){
            $order_amount = $order->bag_amount;
        }else{
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['total_amount'];
        } 

        $total_percent = 0;

        foreach($previsions as $prev){
            if($prev->amount){
                $percent = ($prev->amount * 100) / $order_amount;

                //Si se supera el 100%, se ajusta la última previsión:
                $total_percent = $total_percent + round($percent, 2);
                if($total_percent > 100){
                    $diff = $total_percent - 100;
                    $percent = $percent - $diff;
                }

                $prev->month = $prev->month? $prev->month:Date('m');
                $prev->percent = $percent;
                $prev->save();
            }

            //En caso de facturación total, se eliminan las previsiones no cumplidas y sin facturar:
            if(!$prev->invoice_id && $all_invoiced){
                $prev->delete();
            }
        }
    }

    /**
     * 3. Previsiones de facturación por pedido.
     *
     * 28/11/2023: incluimos el parámetro $generate para generar nuevas previsiones en función si el pedido se encuentra o no bloqueado.
     */
    public static function previsions($order_id, $generate = false){
        //Suma de porcentaje de las previsiones:
        $sum_percent = OrderPrevisions::where('order_previsions.order_id', $order_id)
        ->sum('percent');

        //Añadimos nueva previsión si el porcentaje previsto es inferior a 100:
        if($sum_percent < 100 && $generate){
            $diff = 100 - $sum_percent;

            $op = new OrderPrevisions();
            $op->order_id = $order_id;
            $op->year = date('Y');
            $op->percent = $diff;
            $op->save();
        }

        return OrderPrevisions::select('order_previsions.*', 'invoices.id AS invoiceId', 'invoices.invoice_num', 'invoices.amount', 'invoices.amount_with_iva')
        ->leftJoin('invoices', 'order_previsions.invoice_id', '=', 'invoices.id')
        ->where('order_previsions.order_id', $order_id)
        ->orderBy('order_previsions.year', 'ASC')
        ->orderBy('order_previsions.id', 'ASC')
        ->orderBy('order_previsions.month', 'ASC')
        ->get();    
    }

}
