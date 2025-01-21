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
    protected $guarded = [];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_budget' => 'boolean',
        'is_order' => 'boolean',
        'from_customer' => 'boolean',
        'accepted_date' => 'date',
        'is_group' => 'boolean',
        'estimated_delivery_date' => 'date',
        'delivery_date' => 'date',
        'pay_order_received' => 'datetime',
        'customer_confirmation' => 'datetime',
        'confirm_code_date' => 'datetime',
        'invoiced' => 'boolean',
    ];


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
     * 1.1. Facturas del pedido.
     */
    public function invoices(){
        return $this->hasMany(Invoice::class);
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
            $total_amount = $total_amount + $amount['total_amount'];
        }

        return $total_amount;
    }

    /**
     * 5. Cálculo importe items.
     *
     * Collection $items    var $percentage_items
     * 
     * Agrupamos las líneas según su tipo de IVA.
     */
    public function itemsAmount($items, $order_discount = false, $percentage_items = false, $with_iva = false){
        //Extraemos los tipos de IVA:
        if($with_iva){
            $iva_types = [];
            foreach($items as $row){
                if(!in_array($row->iva, $iva_types)){
                    array_push($iva_types, $row->iva);
                }
            }
        }

        $order_amount = 0;                      //Importe del pedido sin impuestos (Base imponible).
        $order_amount_without_discounts = 0;    //Importe del pedido sin impuestos ni descuentos.
        $invoice_amount = 0;                    //Importe con impuestos.
            
        $amounts_by_iva = [];                   //Array que agrupa la suma de importes según tipo de IVA.
        $iva_amounts = [];                      //Array que agrupa el importe de cada tipo de IVA.

        $arr_invoice_amount = [
            'amount' => '',
            'amount_without_discount' => '',
            'amount_with_iva' => '',
            'iva_amount' => ''
        ];

        foreach($items as $row){
            //Omitimos los items que se calculan porcentualmente:
            if(!$row->calculated_by_percent && $row->price != 0){
                //Base imponible de la línea:
                $arr_order_amount = helpers::itemAmount($row, false);

                //Aplicando descuento del pedido:
                //22/03/2023: se bloquea este script pues los descuentos se aplican previamente sobre cada línea.
                // if($order_discount && $order_discount > 0){
                //     $percentage_ = helpers::percent($arr_order_amount['amount'], $order_discount);    
                //     $arr_order_amount['amount'] = $arr_order_amount['amount'] - $percentage_;    
                // }


                // if($apply_row_discount && $row->discount && $row->discount > 0){
                //     $percentage_ = helpers::percent($arr_order_amount['amount'], $row->discount);    
                //     $arr_order_amount['amount'] = $arr_order_amount['amount'] - $percentage_;         
                // }


                $order_amount = $order_amount + $arr_order_amount['amount'];
                $order_amount_without_discounts = $order_amount_without_discounts +  $arr_order_amount['amount_without_discount'];

                //Cálculo de importe de la línea (con impuestos):
                $arr_invoice_amount = helpers::itemAmount($row, true);
                $invoice_amount = $invoice_amount + $arr_invoice_amount['amount'];

                $amounts_by_iva[$row->iva] = isset($amounts_by_iva[$row->iva])? $amounts_by_iva[$row->iva] + $arr_invoice_amount['amount']:$arr_invoice_amount['amount'];

                $iva_amounts[$row->iva] = isset($iva_amounts[$row->iva])? $iva_amounts[$row->iva] + $arr_invoice_amount['iva_amount']:$arr_invoice_amount['iva_amount'];

                //11/12/22: QUEDA PENDIENTE EL CÁLCULO DEL RECARGO DE EQUIVALENCIA.
            }
        }

        //Cálculo de los items por porcentaje:
        if($percentage_items){
            $order_amount_without_percentuals = $order_amount;
            $order_amount_without_percentuals_without_discounts = $order_amount_without_discounts;

            foreach($items as $row){
                if($row->calculated_by_percent){
                    // $amount = $row->calculated_by_percent * $total_amount_without_percentuals;
                    $amount = helpers::itemAmountPercentage($row->price, $order_amount_without_percentuals);
                    $order_amount = $order_amount + $amount;

                    //Sumando porcentaje al importe sin descuentos:
                    $amount_wd = helpers::itemAmountPercentage($row->price, $order_amount_without_percentuals_without_discounts);
                    $order_amount_without_discounts = $order_amount_without_discounts + $amount_wd;

                    //Cálculo de importe de la línea (con impuestos):
                    $percent = ($amount * $row->iva) / 100;
                    $amount_with_iva = round($amount + $percent, 2);
                    $invoice_amount = $invoice_amount + $amount_with_iva;

                    $amounts_by_iva[$row->iva] = isset($amounts_by_iva[$row->iva])? $amounts_by_iva[$row->iva] + $amount_with_iva:$amount_with_iva;

                    $iva_amounts[$row->iva] = isset($iva_amounts[$row->iva])? $iva_amounts[$row->iva] + $arr_invoice_amount['iva_amount']:$arr_invoice_amount['iva_amount'];

                    //11/12/22: QUEDA PENDIENTE EL CÁLCULO DEL RECARGO DE EQUIVALENCIA.
                }
            }
        }

        $data = [
            'total_amount' => round($order_amount, 2), 
            'total_amount_without_discounts' => round($order_amount_without_discounts, 2),
            'invoice_amount' => round($invoice_amount, 2),
            'amounts_by_iva' => $amounts_by_iva,        //Desglose importe total separado por IVA.
            'iva_amounts' => $iva_amounts               //Importe de cada tipo de IVA.
        ];

        return $data;
    }

    /**
     * 6. Suma importe de líneas por presupuesto-pedido.
     */
    public function itemsBudgetOrderAmount($percentage_items = false){
        $items = Item::select('id', 'qty', 'price', 'discount', 'days', 'days_continue', 'days_continue_discount', 'calculated_by_percent')
            ->where('order_id', $this->id)
            ->get();

        $total_amount = $this->itemsAmount($items, $this->discount, $percentage_items);

        return round($total_amount['total_amount'], 2);
    }

    /**
     * 7. Importe del presupuesto-pedido.
     *
     */
    public function getAmount($discount_ = true, $with_iva = false, $with_percentage_items = true){
        $items = Item::select('id', 'concept', 'qty', 'price', 'item_no_price', 'iva', 'equivalence_surcharge', 'irpf', 'discount', 'calculated_by_percent', 'days', 'days_continue', 'days_continue_discount')
            ->where('order_id', $this->id)
            ->get();

        //$arr_amount = $this->itemsAmount($items, $this->discount, $with_percentage_items, $with_iva);
        //$amount = $arr_amount['total_amount'];

        //Aplicando descuento del presupuesto-pedido:
        //11/12/22. El descuento se aplica previamente sobre cada línea del presupuesto, en lugar de aplicarlo globalmente. De este modo se puede calcular IVA para pedidos con productos con distinto tipo de IVA.
        // $amount_discount = $amount;
        // if($discount_ && $this->discount && $this->discount > 0){
        //     $percent = helpers::percent($amount_discount, $this->discount);
        //     $amount_discount = $amount_discount - $percent;
        // }

        //$data['amount_without_discount'] = $amount;
        //$data['amount_with_discount'] = $amount_discount;
        //$data['amount_without_discount_in_lines'] = $arr_amount['total_amount_without_discounts'];  //Total sin considerar el descuento aplicado en líneas.

        //return $data;
        
        return $this->itemsAmount($items, $this->discount, $with_percentage_items, $with_iva);
    }

    /**
     * 8. Importe facturado por pedido.
     */
    public function getInvoicedAmount(){
        //La selección de facturas debe omitir las facturas de abono y las devueltas.
        //¡Atención! se incluye la condición deleted_at IS NULL pues el borrado lógico no parece funcionar cuando se incluye Query Builder (DB::)
        // $data = Invoice::select('id',
        //     DB::raw("(SELECT SUM(amount) FROM invoices
        //         WHERE order_id = " . $this->id . "
        //         AND refund IS NULL
        //         AND deleted_at IS NULL
        //         AND status != " . config('constants.INVOICE_REFUNDED_') . ") AS invoiced_amount"),
        //     DB::raw("(SELECT SUM(amount_with_iva) FROM invoices
        //         WHERE order_id = " . $this->id . "
        //         AND refund IS NULL
        //         AND deleted_at IS NULL
        //         AND status != " . config('constants.INVOICE_REFUNDED_') . ") AS invoiced_amount_with_iva")
        // )
        // ->where('order_id', $this->id)
        // ->first();
        
        // Obtener la suma total de los importes de las facturas.
        $totalAmounts = Invoice::where('order_id', $this->id)
        ->whereNull('deleted_at')
        ->whereNull('refund')
        //->where('status', '!=', config('constants.INVOICE_REFUNDED_'))
        ->select(
            DB::raw("SUM(amount) AS total_amount"),
            DB::raw("SUM(amount_with_iva) AS total_amount_with_iva")
        )
        ->first();
    
        // Obtener la suma total de los reembolsos.
        $totalRefunds = Invoice::where('order_id', $this->id)
        ->where('deleted_at', NULL)
        ->whereNotNull('refund')
        //->where('status', '!=', config('constants.INVOICE_REFUNDED_'))
        ->select(
            DB::raw("SUM(amount) AS total_refund"),
            DB::raw("SUM(amount_with_iva) AS total_refund_with_iva")
        )
        ->first();

        if($totalAmounts){
            // Calcular los importes finales.
            $data['invoiced_amount'] = ($totalAmounts->total_amount ?? 0) - ($totalRefunds->total_refund ?? 0);
            $data['invoiced_amount_with_iva'] = ($totalAmounts->total_amount_with_iva ?? 0) - ($totalRefunds->total_refund_with_iva ?? 0);
        }else{
            $data = false;
        }
    
        return $data;
    }

    /**
     * 9. Aplicar descuento por defecto de cliente.
     *
     * No se aplica para las agrupaciones. Sólo se aplica el descuento que puedan tener los pedidos que la conforman.
     */
    public function setDefaultDiscount(){
        if (!$this->is_group) {
            $settings = CustomerProviders::where('customer_id', $this->customer_id)
                ->where('provider_id', $this->provider_id)
                ->first();

            if ($settings->discount > 0) {
                $this->discount = $settings->discount;
                $this->save();
            }
        }
    }

    /**
     * 10. Proyectos asociados.
     */
    public function projects(){
        return $this->belongsToMany(Project::class, 'project_orders');
    }

    public function budgetPattern()
    {
        return $this->belongsTo(BudgetPatterns::class);
    }

    public function budgetForm()
    {
        return $this->belongsTo(BudgetForms::class, 'form_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function budgetSetting()
    {
        return $this->belongsTo(BudgetSettings::class, 'budget_template_id');
    }

    public function orderPattern()
    {
        return $this->belongsTo(OrderPatterns::class);
    }

    public function orderSetting()
    {
        return $this->belongsTo(OrderSettings::class, 'order_template_id');
    }

    public function purchasePattern()
    {
        return $this->belongsTo(PurchasePatterns::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'customer_id');
    }

    public function workplace()
    {
        return $this->belongsTo(Workplace::class, 'customer_workplace_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenters::class, 'customer_cost_center_id');
    }

    public function businessArea()
    {
        return $this->belongsTo(
            BusinessAreas::class,
            'customer_business_area_id'
        );
    }

    public function confimedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function provider()
    {
        return $this->belongsTo(Company::class, 'provider_id');
    }

    public function providerManager()
    {
        return $this->belongsTo(User::class, 'provider_manager_id');
    }

    public function providerBusinessArea()
    {
        return $this->belongsTo(
            BusinessAreas::class,
            'provider_business_area_id'
        );
    }

    public function finalCustomer()
    {
        return $this->belongsTo(Company::class, 'final_customer_id');
    }

    public function finalCustomerManager()
    {
        return $this->belongsTo(User::class, 'final_customer_manager_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function providerWorkplace()
    {
        return $this->belongsTo(Workplace::class, 'provider_workplace_id');
    }

    public function customerManager()
    {
        return $this->belongsTo(User::class, 'customer_manager_id');
    }

    public function providerCostCenterId()
    {
        return $this->belongsTo(CostCenters::class, 'provider_cost_center_id');
    }

    public function previsions(){
        return $this->hasMany(OrderPrevisions::class);
    }

    

}
