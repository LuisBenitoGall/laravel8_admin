<?php

namespace App\Models;

use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceItems extends Model{
    /**
     * 1. Guardar líneas de factura.
     * 2. Líneas de factura.
     * 3. Recalcular importe factura por sus líneas.
     * 4. Actualizar línea de factura.
     */
    
    use HasFactory;

    protected $table = 'invoice_items';

    /**
     * 1. Guardar líneas de factura.
     */
    public static function saveInvoiceItems($invoice, $data){
        //Guardamos los items de factura en función del origen: por pedido o albarán:
        if($data['origin'] == 'order'){
            $items = Item::select('items.id', 'items.qty', 'items.price', 'items.iva', 'items.discount', 'items.days', 'items.days_continue', 'items.days_continue_discount')
            ->where('items.order_id', $data['order_id'])
            ->orderBy('items.budget_sort', 'ASC')
            ->get();  
        
        }else{
            $items = DeliveryItem::select('delivery_items.qty', 'delivery_items.delivery_id', 'items.id', 'items.price', 'items.iva', 'items.discount', 'items.days', 'items.days_continue', 'items.days_continue_discount')
            ->join('items', 'delivery_items.item_id', '=', 'items.id')
            ->whereIn('delivery_items.delivery_id', $data['deliveries'])
            ->orderBy('delivery_items.sort', 'ASC')
            ->get();  
        }

        foreach($items as $item){
            $amount = helpers::itemAmount($item, true);

            //Calculamos el porcentaje de cada línea sobre el total facturado:
            $amount_ = $amount['amount'] > 0? ($amount['amount'] * 100) / $invoice->amount:'0';

            $ii = new InvoiceItems();
            $ii->invoice_id = $invoice->id;
            $ii->item_id = $item->id;
            $ii->qty = $item->qty;
            $ii->price = $item->price;
            $ii->discount = $item->discount? $item->discount:'0';
            $ii->days = $item->days;
            $ii->days_continue = $item->days_continue;
            $ii->days_continue_discount = $item->days_continue_discount;
            $ii->amount = $amount_;
            $ii->iva = $item->iva;
            $ii->save();
        }  
    }

    /**
     * 2. Líneas de factura.
     */
    public static function getInvoiceItems($invoice_id){
        $data = InvoiceItems::select('items.concept', 'items.iva AS productIva', 'invoice_items.*')
        ->leftJoin('items', 'invoice_items.item_id', '=', 'items.id')
        ->where('invoice_items.invoice_id', $invoice_id)
        ->get();

        return $data;
    }

    /**
     * 3. Recalcular importe factura por sus líneas.
     */
    public static function setInvoiceAmount($invoice){
        $items = InvoiceItems::select('amount', 'iva')
        ->where('invoice_id', $invoice->id)
        ->get();


        //En caso de que la factura tenga importe y no se encuentren líneas de factura, se recuperan del pedido hasta cuadrar el importe de la factura.
        if(!$items->count() && $invoice->amount > 0){
            $order_items = Item::select('id', 'qty', 'price', 'item_no_price', 'iva', 'discount', 'calculated_by_percent', 'days', 'days_continue', 'days_continue_discount')
            ->where('order_id', $invoice->order_id)
            //->whereNotNull('invoiced')
            ->get();

            $invoice_amount = 0;

            foreach($order_items as $oi){
                $amount = helpers::itemAmount($oi);
                $invoice_amount = $invoice_amount + $amount['amount'];

                //Mientras el importe de la factura sea inferior al importe del pedido:
                if($invoice_amount <= $invoice->amount){
                    $row = new InvoiceItems();
                    $row->invoice_id = $invoice->id;
                    $row->item_id = $oi->id;
                    $row->qty = $oi->qty;
                    $row->price = $oi->price;
                    $row->discount = $oi->discount? $oi->discount:'0';
                    $row->days = $oi->days? $oi->days:'0';
                    $row->days_continue = $oi->days_continue? $oi->days_continue:'0';
                    $row->days_continue_discount = $oi->days_continue_discount;
                    $row->amount = $amount['amount'];
                    $row->iva = $oi->iva;
                    $row->save(); 
                }     
            }

            //Recuperamos de nuevos los items de la factura:
            $items = InvoiceItems::select('amount', 'iva')
            ->where('invoice_id', $invoice->id)
            ->get();
        }

        $amount = 0;
        $amount_with_iva = 0;

        foreach($items as $i){
            $percent = $i->iva && $i->iva > 0? ($i->amount * $i->iva) / 100:'0';
            $precio = round($i->amount + $percent, 2);

            $amount = $amount + $i->amount;
            $amount_with_iva = $amount_with_iva + $precio;
        }

        $invoice->amount = $amount;
        $invoice->amount_with_iva = $amount_with_iva;
        $invoice->save();

        $data = [
            'amount' => $amount,
            'amount_with_iva' => $amount_with_iva
        ];        
        return $data;
    }
    
    /**
     * 4. Actualizar línea de factura.
     */
    public static function updateInvoiceItem($item, $invoice_id){
        $row = InvoiceItems::where('invoice_id', $invoice_id)
        ->where('item_id', $item->id)
        ->first();

        if($row){
            $with_iva = $row->iva > 0? true:false;

            $amount = helpers::itemAmount($item, $with_iva);
            
            $row->qty = $item->qty;
            $row->price = $item->price;
            $row->discount = $item->discount? $item->discount:'0';
            $row->days = $item->days? $item->days:'0';
            $row->days_continue = $item->days_continue? $item->days_continue:'0';
            $row->days_continue_discount = $item->days_continue_discount;
            $row->amount = $amount['amount'];
            $row->iva = $item->iva;
            $row->save();    
        }
    }
}
