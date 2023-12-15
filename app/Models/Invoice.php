<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\helpers;

class Invoice extends Model{
    /**
     * 1. Calcular importe de factura.
     * 1.1. Cálculo factura por items.
     * 1.2. Cálculo factura por items de factura.
     * 2. Suma de importes por pedido.
     */

    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'invoices';

    /**
     * 1. Calcular importe de factura.
     */
    public function calculateAmount(){
        if($this->with_iva > 0){
            $percent = helpers::percent($this->amount, $this->with_iva); 
            $iva_amount = $this->amount + $percent;
        }else{
            $iva_amount = $this->amount;    
        }

        //Ajuste de IVA:
        $iva_amount = $iva_amount + $this->iva_adjust;

        $this->amount_with_iva = $iva_amount;
        $this->save();
    }

    /**
     * 1.1. Cálculo factura por items.
     */
    public function calculateAmountByItems(){
        $items = $this->order_id? Item::select('id', 'qty', 'price', 'item_no_price', 'irpf', 'iva', 'equivalence_surcharge', 'days', 'days_continue', 'days_continue_discount')
        ->where('order_id', $this->order_id)
        ->get():false;

        $amount = 0;
        $amount_with_iva = 0;

        foreach($items as $i){
            $arr_amount = helpers::itemAmount($i, true);

            $amount = $amount + $arr_amount['amount_without_iva'];
            $amount_with_iva = $amount_with_iva + $arr_amount['amount'];
        }

        //Ajuste de IVA:
        $amount_with_iva = $amount_with_iva + $this->iva_adjust;
        
        $this->amount = $amount;
        $this->amount_with_iva = $amount_with_iva;
        $this->save();
    }

    /**
     * 1.2. Cálculo factura por items de factura.
     */
    public function calculateAmountByInvoiceItems(){
        $items = InvoiceItems::select('id', 'amount', 'iva')
        ->where('invoice_id', $this->id)
        ->get();

        $amount = 0;
        $amount_with_iva = 0;

        foreach($items as $i){
            $amount = $amount + $i->amount;

            //$iva = $i->iva > 0? round(($i->amount * $i->iva) / 100, 2):0;
            //$amount_with_iva = $amount_with_iva + $i->amount + $iva;
        }

        //Ajuste de IVA:
        $amount_with_iva = $amount_with_iva + $this->iva_adjust;
        
        $this->amount = $amount;
        $this->amount_with_iva = $amount_with_iva;
        $this->save();
    }

    /**
     * 2. Suma de importes por pedido.
     */
    public static function amountByOrder($order_id){
        $data = Invoice::select('refund', 'amount')
        ->where('order_id', $order_id)
        ->whereNull('deleted_at')
        ->get();

        $total = 0;
        if($data->count()){
            foreach($data as $row){
                if($row->refund){
                    $total = $total - $row->amount;    
                }else{
                    $total = $total + $row->amount;
                }
            }
        }

        return round($total, 2);      
    }
}
