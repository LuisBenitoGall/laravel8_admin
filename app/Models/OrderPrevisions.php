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

    protected $fillable = ['order_id', 'year', 'month', 'amount', 'percent'];

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
     * 18/01/2024: se introduce de nuevo la generación automática de una previsión con el importe restante sin indicación de mes.
     */
    public static function previsions($order_id, $order_amount, $generate = false){
        if($order_amount > 0){
            $previsions = OrderPrevisions::select('order_previsions.*', 'invoices.refund')
            ->leftJoin('invoices', 'order_previsions.invoice_id', '=', 'invoices.id')
            ->where('order_previsions.order_id', $order_id)
            ->orderBy('order_previsions.invoice_id', 'DESC')
            ->orderBy('order_previsions.id', 'ASC')
            ->get();
            $accumulated_percent = 0;
            $accumulated_amount = 0;

            //Bloqueo de pedido:
            $is_blocked = Order::select('blocked')->find($order_id);

            //07/06/2024: ya no se agrupan las previsiones sin mes en una sola.
            //$previsions_without_month = [];

            $all_invoiced = true;

            //Número de previsiones para controlar la última:
            $num_previsions = $previsions->count();
            $k = 1;

            foreach($previsions as $p){
                //Se omiten las previsiones relativas a abonos de factura.
                if(!$p->refund){
                    //Previsión facturada:
                    
                    if($p->invoice_id){
                        //Importe de la factura:
                        $invoice_amount = Invoice::select('amount')->find($p->invoice_id);

                        //Porcentaje sobre el total del pedido:
                        if($order_amount > 0){
                            $percent = round( ($invoice_amount->amount * 100) / $order_amount, 6);
                        }else{
                            $percent = 100;
                        }

                        $accumulated_percent = $accumulated_percent + $percent;
                        $accumulated_amount = $accumulated_amount + $invoice_amount->amount;

                        //Si no se supera el importe del pedido, se actualiza la línea:
                        if($accumulated_amount <= $order_amount){
                            $p->amount = $invoice_amount->amount;
                            $p->percent = $percent;
                        }

                    //No facturada:
                    //19/01/2024: las previsiones sin mes asignado se agrupan en una sola.
                    }else{

                        $all_invoiced = false;
                        $amount = 0;

                        //Para 1 única previsión:
                        // if($previsions->count() == 1){
                        //     $p->amount = $order_amount;
                        //     $p->percent = 100;

                        // }else{
                        
                        //Con mes declarado:
                        //07/06/2024: ya no se agrupan las previsiones sin mes en una sola.
                        //if($p->month){
                            //Verificamos que el importe se ajuste al total:
                            $percent = NULL;
                            if(!$p->amount || $p->amount == '0'){
                                $amount = $order_amount - $accumulated_amount;
                                $percent = round( ($amount * 100) / $order_amount, 6);

                            }elseif($p->amount > 0){
                                $amount = $p->amount <= ($order_amount - $accumulated_amount) + config('constants.TOLERANCE_DECIMALS_')? $p->amount:NULL;

                                //Ajuste del porcentaje:
                                if($amount > 0){
                                    $percent = round( ($amount * 100) / $order_amount, 6);
                                }
                            }

                            //Actualizando importe de la última previsión activa:
                            if($k == $num_previsions && !$p->invoice_id){
                                $amount = $order_amount - $accumulated_amount;
                                $percent = 100 - $accumulated_percent;
                            }

                            $accumulated_percent = $accumulated_percent + $percent;
                            $accumulated_amount = $accumulated_amount + $amount;

                            $p->amount = $amount;
                            $p->percent = $percent;

                        //Sin mes declarado:
                        // }else{
                        //     array_push($previsions_without_month, $p);    
                        // }
                    }

                    $p->save();

                    $k++;
                }
            }

            $diff_percent = bcsub(100, $accumulated_percent, 6);
            $diff_amount = $order_amount - $accumulated_amount;

            //Ajustamos porcentaje pendiente en función del importe pendiente:
            if($diff_amount < config('constants.TOLERANCE_DECIMALS_')){
                $diff_percent = 0;    
            }

            //Agrupando previsiones sin mes:
            // $first_id = false;
            // if(count($previsions_without_month)){
            //     $first_id = $previsions_without_month[0]->id;
            // }

            //Añadimos nueva previsión o actualizamos una existente - condiciones:  
            //1. si el porcentaje previsto es superior a 0.01 
            //2. están facturadas todas las previsiones anteriores: 
            //3. el pedido no está bloqueado. 
            if($diff_amount >= config('constants.TOLERANCE_DECIMALS_') && $all_invoiced && !$is_blocked){
                // if($first_id){
                //     OrderPrevisions::where('id', $first_id)
                //     ->update(['amount' => round($diff_amount, 2), 'percent' => $diff_percent]);    

                // }else{
                    $op = new OrderPrevisions();
                    $op->order_id = $order_id;
                    $op->year = date('Y');
                    $op->amount = round($diff_amount, 2);
                    $op->percent = $diff_percent;
                    $op->save();
                //}
            }
        }

        return OrderPrevisions::select('order_previsions.*', 'invoices.id AS invoiceId', 'invoices.refund', 'invoices.invoice_num', 'invoices.amount AS invoice_amount', 'invoices.amount_with_iva')
        ->leftJoin('invoices', 'order_previsions.invoice_id', '=', 'invoices.id')
        ->where('order_previsions.order_id', $order_id)
        ->orderBy('order_previsions.year', 'ASC')
        ->orderByRaw('order_previsions.invoice_id IS NULL, order_previsions.invoice_id ASC')
        ->orderByRaw('order_previsions.month IS NULL, order_previsions.month ASC')
        ->orderBy('order_previsions.id', 'ASC')
        ->get();  
    }
}
