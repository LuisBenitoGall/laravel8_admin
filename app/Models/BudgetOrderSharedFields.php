<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BudgetOrderSharedFields extends Model{
    /**
     * 1. Items por presupuesto - pedido.
     * 2. Cálculo importe items.
     * 3. Suma importe de líneas por presupuesto - pedido.
     * 4. Importe del presupuesto-pedido.
     * 5. Importe facturado.
     */
     
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'budget_order_shared_fields'; 

    protected $dates = ['deleted_at'];

    // Relaciones:
    public function budget(){
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function customer(){
        return $this->belongsTo(Company::class, 'customer_id');    
    }


    /**
     * 1. Items por presupuesto - pedido.
     */
    public function getBudgetOrderItems(){
        return Item::where('budget_order_id', $this->id)
        ->get();  
    }

    /**
     * 2. Cálculo importe items.
     *
     * Collection $items    var $percentage_items
     */
    public function itemsAmount($items, $percentage_items = false, $with_iva = false){
        $total_amount = 0;
        foreach($items as $row){
            //Omitimos los items que se calculan porcentualmente:
            if(!$row->calculated_by_percent && $row->price > 0){
                //Cálculo de importe de la línea:
                $amount = Helpers::itemAmount($row, $with_iva = false);
                $total_amount = $total_amount + $amount;
            }    
        }

        //Cálculo de los items por porcentaje:
        if($percentage_items){
            $total_amount_without_percentuals = $total_amount;

            foreach($items as $row){
                if($row->calculated_by_percent){
                    // $amount = $row->calculated_by_percent * $total_amount_without_percentuals;
                    $amount = Helpers::itemAmountPercentage($row->calculated_by_percent, $total_amount_without_percentuals);
                    $total_amount = $total_amount + $amount;
                }
            }
        }

        return round($total_amount, 2);
    }

    /**
     * 3. Suma importe de líneas por presupuesto-pedido.
     */
    public function itemsBudgetOrderAmount($percentage_items = false){
        $items = Item::select('id', 'qty', 'price', 'discount', 'days', 'days_continue', 'days_continue_discount', 'calculated_by_percent')
        ->where('budget_order_id', $this->id)
        ->get();

        $total_amount = $this->itemsAmount($items, $percentage_items);

        return round($total_amount, 2);
    }

    /**
     * 4. Importe del presupuesto-pedido.
     */
    public function getAmount($discount_ = true, $with_iva = false){
        $items = $this->getBudgetOrderItems();

        $amount = $this->itemsAmount($items, true, $with_iva);

        //Aplicando descuento del presupuesto-pedido:
        $amount_discount = $amount;
        if($discount_ && $this->discount && $this->discount > 0){
            $percent = Helpers::percent($amount_discount, $this->discount);
            $amount_discount = $amount_discount - $percent;
        }

        $data['amount_without_discount'] = $amount;
        $data['amount_with_discount'] = $amount_discount;

        return $data;
    }

    /**
     * 5. Importe facturado.
     */
    public function getInvoicedAmount(){
        $data = Invoice::select('id',
            DB::raw("(SELECT SUM(amount) FROM invoices WHERE shared_id = ".$this->id.") AS invoiced_amount"),
            DB::raw("(SELECT SUM(amount_with_iva) FROM invoices WHERE shared_id = ".$this->id.") AS invoiced_amount_with_iva")
        )
        ->where('shared_id', $this->id)
        ->first();
        // 

        // $data = Invoice::where('shared_id', $this->id)
        // ->get();
         
        // $data = Invoice::where('shared_id', $this->id)
        // ->get()
        // ->sum('amount_with_iva')
        // ->sum('amount'); 
        
        //dd($data);

        return $data;
    }
}
