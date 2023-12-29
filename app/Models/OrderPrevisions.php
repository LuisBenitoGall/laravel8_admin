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
        $previsions = OrderPrevisions::select('order_previsions.id', 'order_previsions.month', 'order_previsions.percent', 'order_previsions.amount', 'invoices.amount AS invoice_amount', 'order_previsions.invoice_id')
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

        $accumulated_percent = 0;
        $accumulated_amount = 0;

        foreach($previsions as $p){
            if($p->invoice_id){
                $percent = round( ($p->invoice_amount * 100) / $order_amount, 6);

                //Si se supera el 100%, se ajusta la última previsión:
                $accumulated_percent = $accumulated_percent + $percent;
                if($accumulated_percent > 100){
                    $diff = $accumulated_percent - 100;
                    $percent = $percent - $diff;
                }

                $p->month = $p->month? $p->month:Date('m');
                $p->amount = $p->invoice_amount;
                $p->percent = $percent;
                $p->save();
            }

            //En caso de facturación total, se eliminan las previsiones no cumplidas y sin facturar:
            if(!$p->invoice_id && $all_invoiced){
                $p->delete();
            }
        }
    }

    /**
     * 3. Previsiones de facturación por pedido.
     *
     * 28/11/2023: incluimos el parámetro $generate para generar nuevas previsiones en función si el pedido se encuentra o no bloqueado.
     * 29/12/2023: se modifica el método para que recalcule porcentajes e importes de cada previsión, y se incluye importe, que tiene prevalencia sobre el porcentaje.
     */
    public static function previsions($order_id, $order_amount, $generate = false){
        $previsions = OrderPrevisions::where('order_previsions.order_id', $order_id)
        ->orderBy('invoice_id', 'DESC')
        ->orderBy('id', 'ASC')
        ->get();

        $accumulated_percent = 0;
        $accumulated_amount = 0;

        foreach($previsions as $p){
            if($p->invoice_id){
                //Importe de la factura:
                $invoice_amount = Invoice::select('amount')->find($p->invoice_id);

                //Porcentaje sobre el total del pedido:
                $percent = round( ($invoice_amount->amount * 100) / $order_amount, 6);

                $accumulated_percent = $accumulated_percent + $percent;
                $accumulated_amount = $accumulated_amount + $invoice_amount->amount;

                //Si no se supera el importe del pedido, se actualiza la línea:
                if($accumulated_amount <= $order_amount){
                    $p->amount = $invoice_amount->amount;
                    $p->percent = $percent;
                }

            }else{
                //Verificamos que el importe se ajuste al total:
                $amount = $p->amount <= ($order_amount - $accumulated_amount)? $p->amount:NULL;

                //Ajuste del porcentaje:
                $percent = $amount > 0? round( ($amount * 100) / $order_amount, 6):NULL;

                $p->amount = $amount;
                $p->percent = $percent;
            }

            $p->save();
        }

        //Añadimos nueva previsión si el porcentaje previsto es inferior a 100:
        // if($sum_percent < 100 && $generate){
        //     $diff = 100 - $sum_percent;

        //     $op = new OrderPrevisions();
        //     $op->order_id = $order_id;
        //     $op->year = date('Y');
        //     $op->percent = $diff;
        //     $op->save();
        // }

        return OrderPrevisions::select('order_previsions.*', 'invoices.id AS invoiceId', 'invoices.invoice_num', 'invoices.amount AS invoice_amount', 'invoices.amount_with_iva')
        ->leftJoin('invoices', 'order_previsions.invoice_id', '=', 'invoices.id')
        ->where('order_previsions.order_id', $order_id)
        ->orderBy('order_previsions.year', 'ASC')
        ->orderBy('order_previsions.month', 'ASC')
        ->orderBy('order_previsions.id', 'ASC')
        ->get();    
    }

}
