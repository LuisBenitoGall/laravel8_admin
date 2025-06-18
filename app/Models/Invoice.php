<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Helpers\helpers;

class Invoice extends Model{
    /**
     * 1. Calcular importe de factura.
     * 1.1. Cálculo factura por items.
     * 1.2. Cálculo factura por items de factura.
     * 2. Suma de importes por pedido.
     * 3. Generar factura.
     * 3.1. Asiento contable de factura de venta.
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
     */
    public static function setInvoice($order, $data){
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

        //Pagas y señales:
        $sign_payments = Payment::where('order_id', $order->id)
        ->where('is_pay_sign', 1)
        ->sum('amount');

        //Condiciones cliente y proveedor:
        $customer_provider = CustomerProviders::select('accounting_concept_id', 'pay_after_days', 'payday', 'payment_method_id', 'num_deadlines', 'days_between_deadlines')
        ->where('customer_id', $order->customer_id)
        ->where('provider_id', $order->provider_id)
        ->first();

        $fiscal_date = isset($data['fiscal_date']) && $data['fiscal_date']? $data['fiscal_date']:date('Y-m-d');

        //Cálculo fecha vencimiento:
        $expiration_date = helpers::calculateExpirationDate($fiscal_date, $customer_provider->pay_after_days, $customer_provider->payday);

        //Crear una instancia de la clase actual:
        $instance = new self();
        $instance->order_id = $order->id;
        $instance->user_id = Auth::user()->id;
        $instance->amount = $data['total_amount'];
        $instance->amount_priority = $data['amount_priority'];
        $instance->with_iva = CompanySettings::co_iva();
        $instance->amount_with_iva = $data['amount_with_iva'];
        $instance->fiscal_date = $fiscal_date;
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

        //Actualizamos los valores de la siguiente previsión recibida:
        // $next_previsions_to_invoice = OrderPrevisions::where('order_id', $order->id)
        // ->whereNull('invoice_id')
        // ->orderBy('year', 'ASC')
        // ->orderBy('month', 'ASC')
        // ->orderBy('id', 'ASC')
        // ->first();

        // $next_previsions_to_invoice = OrderPrevisions::where('order_id', $order->id)
        // ->whereNull('invoice_id')
        // ->orderBy('year', 'ASC')
        // ->when(
        //     OrderPrevisions::where('order_id', $order->id)->whereNull('invoice_id')->whereNotNull('month')->exists(),
        //     function ($query) {
        //         return $query->orderBy('month', 'ASC');
        //     }
        // )
        // ->orderBy('id', 'ASC')
        // ->first();


        // if($next_previsions_to_invoice){
        if(isset($data['prevision_id']) && $data['prevision_id']){
            // $next_previsions_to_invoice->invoice_id = $instance->id;
            // if($data['percentage_invoiced']){
            //     $next_previsions_to_invoice->month = $next_previsions_to_invoice->month? $next_previsions_to_invoice->month:Date('m');
            //     $next_previsions_to_invoice->percent = $data['percentage_invoiced'];
            // }
            // $next_previsions_to_invoice->save();
            
            $prevision = OrderPrevisions::find($data['prevision_id']);

            if($prevision){
                $prevision->month = $prevision->month? $prevision->month:date('m');
                //$prevision->percent = $data['percentage_invoiced']? $data['percentage_invoiced']:NULL;
                $prevision->invoice_id = $instance->id;
                $prevision->save();
            }

        //Si no existe previsión, generamos una nueva:
        }else{
            $percent = $data['total_amount'] > 0? ($instance->amount * 100) / $data['total_amount']:'0';

            $new_prev = new OrderPrevisions();
            $new_prev->order_id = $order->id;
            $new_prev->year = Date('Y');
            $new_prev->month = Date('m');
            $new_prev->amount = $instance->amount;
            $new_prev->percent = round($percent, 2);
            $new_prev->invoice_id = $instance->id;
            $new_prev->save();    
        }

        //Líneas de la factura:
        //09/09/24 por ahora se omite su generación, no parecen necesarias.
        // $invoice_items_data = [
        //     'origin' => 'order',
        //     'order_id' => $order->id,
        //     'deliveries' => []
        // ];
        // InvoiceItems::saveInvoiceItems($instance, $invoice_items_data);

        //Asiento y efecto contable:
        $add_data = [
            'serie' => $data['serie'],
            'accounting_sale_id' => $order->accounting_sale_id,
            'sign_payments' => $sign_payments,
            'currency_id' => $customer_provider->currency_id? $customer_provider->currency_id:config('constants.CURRENCY_ID_'),
            'order_id' => $order->id,
            'num_deadlines' => $order->num_deadlines,
            'days_between_deadlines' => $order->days_between_deadlines,
            'customer_bank_account' => $bank_account,
            'provider_bank_account' => $provider_bank_account
        ];
        Invoice::setSaleInvoiceSeat($instance, $order, $add_data);

        return $instance;
    }

    /**
     * 3.1. Asiento contable de factura de venta.
     */
    public static function setSaleInvoiceSeat($invoice, $order, $add_data){
        //Importe del pedido:
        $order_amount = $order->getAmount();

        //Líneas del asiento contable:
        $seat_lines = [];
        $iva_lines = [];
        $iva_errors = [];   //Guardo los tipos de IVA no vinculados a ninguna cuenta contable.

        $seat_lines['accounting_account'] = [];
        $seat_lines['type'] = [];
        $seat_lines['debit'] = [];
        $seat_lines['credit'] = [];

        //1. Línea de cliente (DEBE - DEBIT):
        //1.1. Cuenta contable cliente:
        $customer_accounting_account = AccountingAccount::select('id', 'account')
        ->where('company_id', $order->provider_id)
        ->where('company_target', $order->customer_id)
        ->first();

        $seat_lines['accounting_account_id'][] = $customer_accounting_account? $customer_accounting_account->id:NULL;
        $seat_lines['accounting_account'][] = $customer_accounting_account? $customer_accounting_account->account:NULL;
        $seat_lines['type'][] = 'CP';
        $seat_lines['debit'][] = $invoice->amount_with_iva;
        $seat_lines['credit'][] = NULL;

        //2. Líneas de proveedor (HABER - CREDIT):
        //2.1. Cuentas contables proveedor:
        $provider_accounting_accounts = AccountingAccount::select('id', 'account', 'featured')
        ->where('company_id', $order->provider_id)
        ->where('mode', 4)
        ->where('featured', '>', 0)
        ->get();

        //2.2. Cuentas para venta:
        $account_amount_id = NULL;
        $account_amount = NULL;
        
        foreach($provider_accounting_accounts as $paa){
            if($paa->featured == config('constants.ACCOUNT_DEFAULT_SALES_')){
                $account_amount_id = $paa->id;
                $account_amount = $paa->account;
            }
        }

        //2.3. Línea base factura:
        $seat_lines['accounting_account_id'][] = $account_amount_id;
        $seat_lines['accounting_account'][] = $account_amount;
        $seat_lines['type'][] = 'VC';
        $seat_lines['debit'][] = NULL;
        $seat_lines['credit'][] = $invoice->amount;

        //2.4. Cuentas IVA repercutido:
        //2.4.1. Tipos de IVA presentes entre las líneas del pedido.
        $iva_types = IvaType::select('iva_types.id', 'iva_types.iva')
        ->join('items', 'iva_types.iva', '=', 'items.iva')
        ->where('items.order_id', $order->id)
        ->groupBy('iva_types.id')
        ->get();
        $iva_types_id = [];
        $iva_types_value = [];
        foreach($iva_types as $it){
            array_push($iva_types_id, $it->id);
            $iva_types_value[$it->id] = $it->iva;
        }

        //2.4.2. Cuentas contables de IVA según los tipos de IVA presentes en el pedido.
        $iva_accounts = AccountingAccount::select('id', 'account', 'name', 'mode', 'type', 'iva_type_id')
        ->where('company_id', $order->provider_id)
        ->where('company_target', $order->provider_id)
        ->where('account', 'LIKE', config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_SALES_').'%')      //Filtro por prefijo 477 (IVA para ventas)
        ->whereIn('iva_type_id', $iva_types_id)
        ->get();

        $account_iva_id = NULL;
        $account_iva = NULL;

        //2.5. Línea importe del IVA:
        foreach($iva_accounts as $ia){
            //Importe del IVA según el IVA asociado y proporcional al importe de la factura sobre el total del pedido:
            $iva_amount = 0;

            $iva_items = Item::select('id', 'qty', 'price', 'item_no_price', 'iva', 'discount', 'days', 'days_continue', 'days_continue_discount')
            ->where('order_id', $order->id)
            ->where('iva', $iva_types_value[$ia->iva_type_id])
            ->whereNull('deleted_at')
            ->get();

            foreach($iva_items as $ii){
                $ii_amount = helpers::itemAmount($ii, true);

                $iva_amount = $iva_amount + $ii_amount['iva_amount'];
            }

            //Porcentaje facturado:
            $percent_invoiced = round(($invoice->amount * 100) / $order_amount['total_amount'], 6);

            //Con el importe por IVA obtenido sobre cada tipo de IVA, extraemos la parte proporcional sobre el total del pedido.
            $iva_amount_ = ($percent_invoiced * $iva_amount) / 100;

            $iva_lines['accounting_account_id'][] = $ia->id;
            $iva_lines['accounting_account'][] = $ia->account;
            $iva_lines['type'][] = 'VC';
            $iva_lines['debit'][] = NULL;
            $iva_lines['credit'][] = round($iva_amount_, 2);
        }
        

        //Tipo de asiento contable. Hace referencia a los trimestres para la liquidación del IVA (10, 20, 30, 40). Ya no lo utilizan pues hacen la liquidación mensual.
        $seat_type = 30;
        // if($invoice->customer_id == session('currentCompany')){
        //     if()
        //     $seat_type == 10;
        // }elseif($invoice->provider_id == session('currentCompany')){
        //     $seat_type == 20;
        // }        A REVISAR LAS CONDICIONES ANTERIORES.

        $invoiceNum = $invoice->invoice_num? $invoice->invoice_num:($invoice->invoice_old_num? $invoice->invoice_old_num:$invoice->id);

        //Generando asiento contable:
        $data = [
            'company_id' => $order->provider_id,
            'seat_num' => NULL,
            'seat_type' => $seat_type,
            'seat_date' => $invoice->fiscal_date,
            'operation_type' => NULL,
            'diary' => 'dv',
            'concept' => trans('textos.factura_venta').' - '.$invoiceNum,
            'description' => $invoice->invoice_num.' de '.$invoice->fiscal_date,
            'serie' => $add_data['serie'],
            'doc_num' => $invoice->invoice_num,
            'doc' => NULL,
            'sort_num' => $invoice->num_cardinal,
            'company_target' => $order->customer_id,
            'mode' => NULL,
            'currency_id' => $add_data['currency_id'],
            'invoice_id' => $invoice->id,
            'invoice_amount' => $invoice->amount,
            'invoice_amount_iva' => $invoice->amount_with_iva,
            'provider_doc' => NULL,
            'cash_id' => NULL,
            'pay_sign' => NULL,
            // 'accounting_sale_id' => NULL,
            'seat_lines' => $seat_lines,
            'iva_lines' => $iva_lines
        ];
        $seat = Seat::saveSeat($data);
        //SEGÚN SEA FACTURA O TICKET, DISTINTO TRATAMIENTO.

        //Generando efecto contable:
        //Si el efecto es de una agrupación de facturas, poner prefijo A/ delante del número de efecto.
        $effect_amount_total = $invoice->amount_with_iva - $add_data['sign_payments'];

        //Se divide el importe total entre el número de plazos determinado en las condiciones de pago:
        $days_between_deadlines = $add_data['days_between_deadlines'] > 1? $add_data['days_between_deadlines']:30;
        $n_deadlines = $add_data['num_deadlines'];
        $j = 0;

        //Se calcula el importe parcial para cada efecto:
        $effect_amount_partial = round( ($effect_amount_total / $n_deadlines), 2);

        //Verificamos que la suma de los parciales dé el total inicial:
        $diff = $effect_amount_total - $effect_amount_partial * $n_deadlines;
        $last_amount = $effect_amount_partial + $diff / $n_deadlines;
        $last_amount = round($last_amount, 2);

        while($j < $n_deadlines){
            //Fecha de liquidación:
            if($j == 0){
                $date_ = $invoice->fiscal_date;

            //Para plazos > 1, sumamos el intervalo de días entre plazos.
            }else{
                $date_ = date('Y-m-d', strtotime($date_ . ' + '.$days_between_deadlines.' days'));    
            }

            $data_effect = [
                'is_group' => false,
                'parent' => NULL,
                'date' => $date_,
                'provider_id' => $order->provider_id,
                'provider_bank_account_id' => $add_data['provider_bank_account'],
                'customer_id' => $order->customer_id,
                'customer_bank_account_id' => $add_data['customer_bank_account'],
                'invoice_id' => $invoice->id,
                'serie_id' => $add_data['serie'],
                'remittance_id' => NULL,                //31/08/23 La remesa permanece NULL hasta que se incluye el efecto en alguna remesa. Es un proceso posterior y manual.
                'payment_method_id' => $invoice->payment_method_id,
                'payment_status' => NULL,
                'amount' => $j == $n_deadlines - 1? $last_amount:$effect_amount_partial,             //Se descuenta la paga y señal.
                'observations' => NULL,
                'seat_id' => $seat? $seat->id:NULL,
                'currency_id' => $add_data['currency_id'],
                'created_by' => Auth::user()->id,
                'updated_by' => NULL,
                'status' => config('constants.EFFECT_STATUS_PENDENT_'),
                'numerador' => $j + 1
            ];
            Effect::setEffect($data_effect);

            $j++;
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
