<?php

namespace App\Models;

use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceItems extends Model{
    /**
     * 1. Líneas de factura.
     * 2. Recalcular importe factura por sus líneas.
     * 3. Actualizar línea de factura.
     */
    
    use HasFactory;

    protected $table = 'invoice_items';

    /**
     * 1. Líneas de factura.
     */
    public static function getInvoiceItems($invoice_id){
        $data = InvoiceItems::select('items.concept', 'items.iva AS productIva', 'invoice_items.*')
        ->leftJoin('items', 'invoice_items.item_id', '=', 'items.id')
        ->where('invoice_items.invoice_id', $invoice_id)
        ->get();

        return $data;
    }

    /**
     * 2. Recalcular importe factura por sus líneas.
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

            //dd($invoice, $order_items);

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
     * 3. Actualizar línea de factura.
     */
    public static function updateInvoiceItem($item, $invoice_id){
        $row = InvoiceItems::where('invoice_id', $invoice_id)
        ->where('item_id', $item->id)
        ->first();

        if($row){
            $amount = helpers::itemAmount($item);
            
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
