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
     * 3. Marcar líneas como facturadas.
     * 4. Centro de coste del proveedor.
     * 5. Centro de coste del cliente.
     */
     
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'items'; 

    protected $dates = ['deleted_at']; 

    protected $fillable = [
        'product_id', 'order_id', 'budget_form_block_id', 'budget_form_item_id', 'budget_sort', 'concept', 'qty', 'price', 'item_no_price', 'iva', 'discount', 'calculated_by_percent', 'deposit_used', 'observations', 'days', 'days_continue', 'days_continue_discount', 'provider_cost_center_id', 'active', 'status', 'print', 'user_id'
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

            //Descontando IRPF:
            if($item->irpf > 0){
                $irpf_percent = helpers::percent($amount, $item->irpf);
                $amount = $amount - $irpf_percent;
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
        $data = Item::select('id', 'order_id', 'budget_form_block_id', 'budget_sort')
        ->whereNull('budget_sort')
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

            $r->budget_sort = $i;
            $r->save();
        }
    }

    /**
     * 3. Marcar líneas como facturadas.
     *
     * $action  true = línea factura; false = línea no facturada.
     */
    public static function itemsAsInvoiced($order_id, $action){
        $val = $action? date('Y-m-d H:is'):NULL;

        Item::where('order_id', $order_id)
        ->update(['invoiced' => $val]);
    }

    /**
     * 4. Centro de coste del proveedor.
     */
    public function cost_center_provider(){
        return $this->belongsTo(CostCenters::class, 'provider_cost_center_id');
    }

    /**
     * 5. Centro de coste del cliente.
     */
    public function cost_center_customer(){
        return $this->belongsTo(CostCenters::class, 'customer_cost_center_id');
    }

}
