<?php

namespace App\Models;

use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model{
    /**
     * 1. Calcular importe de item.
     * 2. Ordenar items presupuestos de la migración desde Admin6.
     */
     
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'items'; 

    protected $dates = ['deleted_at']; 

    protected $fillable = [
        'product_id', 'budget_order_id', 'budget_form_block_id', 'budget_form_item_id', 'concept', 'qty', 'price', 'item_no_price', 'iva', 'discount', 'calculated_by_percent', 'deposit_used', 'active', 'status', 'print', 'user_id'
    ];

    /**
     * 1. Calcular importe de item.
     */
    public function getItemAmount($item, $set_discount = true){
        if($item->price){
            $amount = helpers::setNumericFloat($item->qty) * helpers::setNumericFloat($item->price);
            
            //Contabilizando jornadas:
            if($item->days > 1){
                $amount = $amount * $item->days;

                //Aplicando descuento de días continuos:
                if($item->days_continue == 1 && $item->days_continue_discount){
                    $dc_percentage = helpers::percent($amount, $item->days_continue_discount);
                    $amount = $amount - $dc_percentage;     
                }
            }

            //Aplicando descuento:
            if($set_discount && is_numeric($item->price) && $item->discount > 0){
                $percentage = helpers::percent($amount, $item->discount);   
                $amount = $amount - $percentage;
            }

            return $amount;
        }else{
            return 0;
        }
    }

    /**
     * 2. Ordenar items presupuestos de la migración desde Admin6.
     */
    public static function orderBudgetItems(){
        $data = Item::select('id', 'order_id', 'budget_form_block_id', 'budget_order')
        ->whereNull('budget_order')
        ->orderBy('order_id', 'ASC')
        ->orderBy('budget_form_block_id', 'ASC')
        ->get();

        $prev_order = false;
        $prev_block = false;
        $i = 1;

        foreach($data as $r){
            $prev_order = $prev_order && $prev_order == $r->order_id? $prev_order:$r->order_id;

            if($prev_block && $prev_block == $r->budget_form_block_id){
                $prev_block = $prev_block;  
                $i++; 
            }else{
                $prev_block = $r->budget_form_block_id;
                $i = 1;
            }

            $r->budget_order = $i;
            $r->save();
        }
    }

}
