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

        $this->amount_with_iva = $iva_amount;
        $this->save();
    }

    /**
     * 1.1. Cálculo factura por items.
     */
    public function calculateAmountByItems(){
        
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

        return $total;      
    }
}
