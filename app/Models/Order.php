<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Helpers\helpers;

class Order extends Model{
    /**
     * 1. Documentos del pedido.
     * 2. Items por pedido.
     * 3. Pedidos por agrupación + facturas.
     * 4. Importe total por agrupación.
     * 5. Cálculo importe items.
     * 6. Suma importe de líneas por presupuesto-pedido.
     * 7. Importe del presupuesto-pedido.
     * 8. Importe facturado por pedido.
     * 9. Aplicar descuento por defecto de cliente.
     */
    
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'orders'; 

    protected $dates = ['deleted_at'];

    // Relaciones:
    public function customer(){
        return $this->belongsTo(Company::class, 'customer_id');    
    }

    /**
     * 1. Documentos del pedido.
     */
    public function docs(){
        return $this->hasMany(OrderDocs::class);
    }

    /**
     * 2. Items por pedido.
     */
    public function items(){
        return $this->hasMany(Item::class);    
    }

    /**
     * 3. Pedidos por agrupación + facturas.
     */
    public function getGroupOrders(){
        $data = Order::select('orders.id', 'orders.is_budget', 'orders.discount', 'orders.bag_amount', 'orders.order_num', 'orders.order_old_num', 'orders.name', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.invoiced', 'orders.budget_num', 'cost_centers.name AS costCenter', 'invoices.id AS invoiceId', 'invoices.invoice_num')
            ->leftJoin('cost_centers', 'orders.customer_cost_center_id', '=', 'cost_centers.id')
            ->leftJoin('invoice_orders', 'orders.id', '=', 'invoice_orders.order_id')
            ->leftJoin('invoices', 'invoice_orders.invoice_id', '=', 'invoices.id')
            ->where('orders.order_parent', $this->id)
            ->where('orders.is_group', false)
            //->where('orders.invoiced', false)
            ->orderBy('orders.invoiced', 'DESC')
            ->groupBy('orders.id')
            ->get();

        return $data;
    }

    /**
     * 4. Importe total por agrupación.
     */
    public function getGroupAmount(){
        $orders = Order::select('id', 'discount', 'invoiced')
        ->where('orders.order_parent', $this->id)
        ->where('orders.is_group', false)  
        ->get();

        $total_amount = 0;

        foreach($orders as $row){
            $amount = $row->getAmount();
            $total_amount = $total_amount + $amount['amount_with_discount'];
        }

        return $total_amount;
    }

    /**
     * 5. Cálculo importe items.
     *
     * Collection $items    var $percentage_items
     */
    public function itemsAmount($items, $percentage_items = false, $with_iva = false){
        $total_amount = 0;
        foreach($items as $row){
            //Omitimos los items que se calculan porcentualmente:
            if(!$row->calculated_by_percent && $row->price != 0){
                //Cálculo de importe de la línea:
                $amount = helpers::itemAmount($row, $with_iva = false);
                $total_amount = $total_amount + $amount;
            }    
        }

        //Cálculo de los items por porcentaje:
        if($percentage_items){
            $total_amount_without_percentuals = $total_amount;

            foreach($items as $row){
                if($row->calculated_by_percent){
                    // $amount = $row->calculated_by_percent * $total_amount_without_percentuals;
                    $amount = helpers::itemAmountPercentage($row->price, $total_amount_without_percentuals);
                    $total_amount = $total_amount + $amount;
                }
            }
        }

        return round($total_amount, 2);
    }

    /**
     * 6. Suma importe de líneas por presupuesto-pedido.
     */
    public function itemsBudgetOrderAmount($percentage_items = false){
        $items = Item::select('id', 'qty', 'price', 'discount', 'days', 'days_continue', 'days_continue_discount', 'calculated_by_percent')
        ->where('order_id', $this->id)
        ->get();

        $total_amount = $this->itemsAmount($items, $percentage_items);

        return round($total_amount, 2);
    }

    /**
     * 7. Importe del presupuesto-pedido.
     */
    public function getAmount($discount_ = true, $with_iva = false, $with_percentage_items = true){
        $items = Item::select('id', 'qty', 'price', 'item_no_price', 'iva', 'discount', 'calculated_by_percent', 'days', 'days_continue', 'days_continue_discount')
        ->where('order_id', $this->id)
        ->get();

        $amount = $this->itemsAmount($items, $with_percentage_items, $with_iva);

        //Aplicando descuento del presupuesto-pedido:
        $amount_discount = $amount;
        if($discount_ && $this->discount && $this->discount > 0){
            $percent = helpers::percent($amount_discount, $this->discount);
            $amount_discount = $amount_discount - $percent;
        }

        $data['amount_without_discount'] = $amount;
        $data['amount_with_discount'] = $amount_discount;

        return $data;
    }

    /**
     * 8. Importe facturado por pedido.
     */
    public function getInvoicedAmount(){
        //La selección de facturas debe omitir las facturas de abono y las devueltas.
        //¡Atención! se incluye la condición deleted_at IS NULL pues el borrado lógico no parece funcionar cuando se incluye Query Builder (DB::)
        $data = Invoice::select('id',
            DB::raw("(SELECT SUM(amount) FROM invoices 
                WHERE order_id = ".$this->id." 
                AND refund IS NULL 
                AND deleted_at IS NULL
                AND status != ".config('constants.INVOICE_REFUNDED_').") AS invoiced_amount"),
            DB::raw("(SELECT SUM(amount_with_iva) FROM invoices 
                WHERE order_id = ".$this->id." 
                AND refund IS NULL 
                AND deleted_at IS NULL
                AND status != ".config('constants.INVOICE_REFUNDED_').") AS invoiced_amount_with_iva")
        )
        ->where('order_id', $this->id)
        ->first();

        return $data;
    }

    /**
     * 9. Aplicar descuento por defecto de cliente.
     *
     * No se aplica para las agrupaciones. Sólo se aplica el descuento que puedan tener los pedidos que la conforman.
     */
    public function setDefaultDiscount(){
        if(!$this->is_group){
            $settings = CustomerProviders::where('customer_id', $this->customer_id)
            ->where('provider_id', $this->provider_id)
            ->first();

            if($settings->discount > 0){
                $this->discount = $settings->discount;
                $this->save();
            }
        }
    }
}
