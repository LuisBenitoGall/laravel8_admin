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
     * 3. Generar factura.
     * 4. Efectos de una factura.
     * 5. Asientos contables de una factura.
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

    /**
     * 3. Generar factura.
     *
     * 27/12/2023 método en desarrollo y pruebas.
     */
    public static function setInvoice($order, $delivery, $other_data){
        //Cuenta bancaria cliente:
        $bank_account_ = BankAccounts::select('id')
        ->where('company_id', $order->customer_id)
        ->where('featured', 1)
        ->where('status', 1)
        ->first();
        $bank_account = $bank_account_? $bank_account_->id:NULL;

        //Cuenta bancaria proveedor:
        $provider_bank_account_ = BankAccounts::select('id')
        ->where('company_id', $order->provider_id)
        ->where('featured', 1)
        ->where('status', 1)
        ->first();
        $provider_bank_account = $provider_bank_account_? $provider_bank_account_->id:NULL;

        //Configuraciones de proveedor:
        $provider_settings = CustomerProviders::select('accounting_concept_id', 'pay_after_days', 'payday', 'payment_method_id')
        ->where('customer_id', $order->customer_id)
        ->where('provider_id', $order->provider_id)
        ->first();

        //Cálculo fecha vencimiento:
        $expiration_date = helpers::calculateExpirationDate(date('Y-m-d'), $provider_settings->pay_after_days, $provider_settings->payday);

        // Crear una instancia de la clase actual:
        $instance = new self();
        $instance->order_id = $order->id;
        $instance->user_id = Auth::user()->id;
        $instance->amount = $total_amount;
        $instance->amount_priority = $amount_priority;
        $instance->with_iva = CompanySettings::co_iva();
        $instance->amount_with_iva = $amount_with_iva;
        $instance->fiscal_date = date('Y-m-d');
        $instance->expiration_date = $expiration_date;
        $instance->bank_account_id = $bank_account;
        $instance->provider_bank_account_id = $provider_bank_account;
        $instance->status = config('constants.INVOICE_OPEN_');
        $instance->save();

        //Marcamos el pedido como facturado:
        if($instance && !$order->invoiced){
            $order->invoiced = true;
            $order->save();

            //Marcamos las líneas del pedido como facturadas:
            Item::itemsAsInvoiced($order->id, true);
        }

        //Actualizamos los valores de la siguiente previsión no facturada:
        $next_previsions_to_invoice = OrderPrevisions::where('order_id', $order->id)
        ->whereNull('invoice_id')
        ->orderBy('year', 'ASC')
        ->orderBy('month', 'ASC')
        ->first();

        if($next_previsions_to_invoice){
            $next_previsions_to_invoice->invoice_id = $invoice->id;
            if($percentage_invoiced){
                $next_previsions_to_invoice->month = $next_previsions_to_invoice->month? $next_previsions_to_invoice->month:Date('m');
                $next_previsions_to_invoice->percent = $percentage_invoiced;
            }
            $next_previsions_to_invoice->save();

        //Si no existe previsión, generamos una nueva:
        }else{
            $percent = ($invoice->amount * 100) / $order_amount['total_amount'];

            $new_prev = new OrderPrevisions();
            $new_prev->order_id = $order->id;
            $new_prev->year = Date('Y');
            $new_prev->month = Date('m');
            $new_prev->amount = $invoice->amount;
            $new_prev->percent = round($percent, 2);
            $new_prev->invoice_id = $invoice->id;
            $new_prev->save();    
        }

    }

    /**
     * 4. Efectos de una factura.
     */
    public function effects(){
        return $this->belongsToMany(Effect::class, 'invoice_effects');
    }

    /**
     * 5. Asientos contables de una factura.
     */
    public function seats(){
        return $this->hasMany(Seat::class, 'invoice_id');
    }
}
