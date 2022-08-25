<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use App\Notifications\PurchaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use PDF;
use Carbon\Carbon;

//Models:
use App\Models\BankAccounts;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Invoice;
use App\Models\InvoiceOrders;
use App\Models\InvoicePatterns;
use App\Models\InvoicePayments;
use App\Models\InvoiceSettings;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderPrevisions;

class InvoiceController extends Controller{
    /**
     * 1. Facturas de clientes.
     * 1.1. Listado de facturas para DataTable.
     * 1.2. Listado de facturas por cliente y proveedor para DataTable.
     * 2. Generar factura desde pedido.
     * 2.1. Generar factura desde agrupación de pedidos.
     * 3. Editar factura.
     * 4. Actualizar factura.
     * 5. Imprimir factura.
     * 6. Setear info para listado de facturas.
     * 7. Actualizar estado de factura.
     * 8. Bloquear factura.
     * 9. Actualizar plantilla de factura.
     * 10. Facturas por pedido.
     * 10.1. Facturas por agrupación.
     * 11. Cancelar factura.
     * 12. Activar - desactivar IVA en factura.
     * 13. Generar factura de abono.
     * 14. Eliminar pedido de factura de agrupación.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        //IVA:
        $this->co_iva = CompanySettings::co_iva();

        $this->middleware('module_setted:accounting');
        $this->middleware('invoice_get', ['only' => ['edit', 'update', 'print', 'block', 'updateTemplate', 'destroy']]);
        $this->middleware('invoice_post', ['only' => ['refund']]);
        $this->middleware('invoice_modality', ['only' => ['createFromOrder']]);
        $this->middleware('invoice_order_remove', ['only' => ['removeFromInvoice']]);
        $this->middleware('order_post', ['only' => ['createFromOrder', 'createFromGroup']]);
        $this->middleware('order_get', ['only' => ['invoicesByOrder']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación generación de factura:
        if(isset($data['submit_generate_invoice'])){
            $data['advance_amount'] = isset($data['advance_amount'])? $data['advance_amount']:'';
            $data['advance_percent'] = isset($data['advance_percent'])? $data['advance_percent']:'';

            return Validator::make($data, [
                'order_id' => 'required|integer',   
                'modality' => 'required',
                'advance_amount' => 'nullable|validate_third_part:p,'.$data['modality'].','.$data['advance_percent'],
                'advance_percent' => 'nullable|numeric|max:100|validate_third_part:p,'.$data['modality'].','.$data['advance_amount']
            ],[
                'advance_amount.validate_third_part' => trans('textos.required_avance_factura'),
                'advance_percent.validate_third_part' => trans('textos.required_avance_factura')
            ]);

        //Validación generación factura agrupación:
        }elseif(isset($data['submit_generate_by_group'])){
            return Validator::make($data, [
                'order_id' => 'required|numeric',
                'orders' => 'required'
            ],[
                'orders.required' => trans('textos.pedidos_requeridos')
            ]); 

        //Validación template:
        }elseif(isset($data['submit_update_template'])){
            return Validator::make($data, [
                'template_id' => 'required|numeric'
            ]); 

        //Validación abono:
        }elseif(isset($data['submit_generate_refund'])){
            return Validator::make($data, [
                'invoice_id' => 'required|numeric',
                'refund' => 'required',
                'concept' => 'required_if:refund,==,p',
                'refund_amount' => 'required_if:refund,==,p'
            ],[
                'concept.required_if' => trans('textos.concepto_obligatorio'),
                'refund_amount.required_if' => trans('textos.importe_obligatorio'),
            ]);

        }
    }

    /**
     * 1. Facturas por cliente o proveedor.
     */
    public function index($year = false, $customer_id = false, $provider_id = false){
        if($customer_id && $customer_id == session('currentCompany')){
            $invoicesType = 'facturas_compras';
        }elseif($provider_id && $provider_id == session('currentCompany')){
            $invoicesType = 'facturas_ventas';
        }else{
            $invoicesType = 'facturas';
        }   
        
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$invoicesType,
            'module' => $this->module,
            'slug' => 'invoices'
        ];  

        if(!$year) $year = Date('Y');

        $customer = $customer_id? Company::find($customer_id):false;
        $provider = $provider_id? Company::find($provider_id):false;

        $customers = false;
        $providers = false;

        //Comprobamos si la empresa hace gestión de clientes:
        if(session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }

        //Comprobamos si la empresa hace gestión de proveedores:
        if(session('companySettings')['providers_management'] == 1){
            $providers = CustomerProviders::getCompanyProviders(session('currentCompany'));
        }

        return view('admin.invoices.index')->with(compact('header', 'year', 'customer_id', 'provider_id', 'customer', 'provider', 'customers', 'providers'));
    }

    /**
     * 1.1. Listado de facturas para DataTable.
     */
    public function getInvoicesBy(Request $request){
        //Si no se informa de proveedor, éste es la empresa en sesión:
        $provider_id = $request->provider_id? $request->provider_id:session('currentCompany');
        $customer_id = false;

        $customerInfo = $request->customer_id > 0? Company::find($request->customer_id):false;
        $providerInfo = $provider_id > 0? Company::find($provider_id):false;
        $currentCompany = session('currentCompany');

        //Si viene declarado el número de factura redirigimos:
        if($request->invoice_id){
            $invoiceId = $request->invoice_id;

            //Comprobamos si el valor recibido corresponde a id, invoice_num o old_invoice:
            $invoice = Invoice::select('id')
            ->where('id', '>=', 1)
            ->where(function($query) use ($invoiceId){
                $query->where('id', $invoiceId)
                ->orWhere('invoice_num', $invoiceId)
                ->orWhere('invoice_old_num', $invoiceId);
            })
            ->where(function($query1) use ($currentCompany){
                $query1->where('customer_id', $currentCompany)
                ->orWhere('provider_id', $currentCompany);
            })
            ->first();

            if(!$invoice){
                $alert = trans('textos.factura_no_existe');
                return redirect()->route('invoices.index')->with(compact('alert'));   
                exit;
            }else{
                return redirect()->route('invoices.edit', $invoice->id);    
                exit;
            }
        }

        //Facturas por número de pedido:
        if($request->order_id){
            $orderId = $request->order_id;

            //Comprobamos si el valor recibido corresponde a id, order_num o old_order:
            $order = Order::select('id')
            ->where('id', '>=', 1)
            ->where(function($query) use ($orderId){
                $query->where('id', $orderId)
                ->orWhere('order_num', $orderId)
                ->orWhere('order_old_num', $orderId);
            })
            ->where(function($query1) use ($currentCompany){
                $query1->where('customer_id', $currentCompany)
                ->orWhere('provider_id', $currentCompany);
            })
            ->first();

            if(!$order){
                $alert = trans('textos.pedido_no_existe');
                return redirect()->route('invoices.index')->with(compact('alert'));   
                exit;
            }else{
                return redirect()->route('invoices.by-order', $order->id);    
                exit;    
            }
        }

        //Facturas por orden de compra:
        if($request->po){
            $po = $request->po;

            //Comprobamos si el valor recibido corresponde a id, order_num o old_order:
            $order = Order::select('id')
            ->where('id', '>=', 1)
            ->where(function($query) use ($po){
                $query->where('purchase_num', $po)
                ->orWhere('purchase_old_num', $po);
            })
            ->where(function($query1) use ($currentCompany){
                $query1->where('customer_id', $currentCompany)
                ->orWhere('provider_id', $currentCompany);
            })
            ->first();

            if(!$order){
                $alert = trans('textos.orden_compra_no_existe');
                return redirect()->route('invoices.index')->with(compact('alert'));   
                exit;
            }else{
                return redirect()->route('invoices.by-purchase', $order->id);    
                exit;    
            }
        }

        //Sólo podemos tomar un criterio, nunca ambos:
        if($request->customer_id){
            $customer_id = $request->customer_id;
            $provider_id = session('currentCompany');   
            session(['invoiceCompanySelected' => ['customer', $request->customer_id]]);
        
        }else{
            $customer_id = false;
            if(!$request->customer_id){
                session(['invoiceCompanySelected' => ['provider', $provider_id]]);   
            }
        }

        if($provider_id && $customer_id){
            return redirect()->route('invoices.index', [$request->year, $customer_id, $provider_id]);
        }else{
            return redirect()->route('invoices.index', [$request->year]);
        }
    }

    /**
     * 1.2. Listado de facturas por cliente y proveedor para DataTable.
     */
    public function getInvoicesDT($year = false, $customer = false, $provider = false){
        $customer = $customer == 0? false:$customer;
        $provider = $provider == 0? false:$provider;

        $query = Invoice::select('invoices.*', 'companies.name', 'companies.tradename', 'cost_centers.name AS costCenter', 'users.name AS Nombre', 'users.surname')
        ->leftJoin('orders', 'invoices.order_id', '=', 'orders.id')
        ->leftJoin('companies', 'orders.customer_id', '=', 'companies.id')
        ->leftJoin('cost_centers', 'orders.provider_cost_center_id', '=', 'cost_centers.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->whereYear('invoices.fiscal_date', $year)
        ->whereNull('invoices.deleted_at');
        //Filtro cliente:
        $providerId = $provider? $provider:session('currentCompany');
        $query->when($customer, function($q) use($customer, $providerId){
            return $q->where('orders.customer_id', $customer)->where('orders.provider_id', $providerId);
        });
        //Filtro proveedor: 
        $query->when($provider, function($q) use($provider){
            return $q->where('orders.provider_id', $provider);
        }); 
        //Filtro por defecto:
        $currentCompany = session('currentCompany');
        $query->when(!$customer && !$provider, function($q) use($currentCompany){
            return $q->where('orders.provider_id', $currentCompany);
        });
        $invoices = $query->orderBy('invoices.created_at', 'DESC')->get(); 

        //Ampliación de información de los objetos:
        $data = $this->setInvoicesData($invoices);
        return datatables($data)->toJson();
    }

    /**
     * 2. Generar factura desde pedido.
     */
    public function createFromOrder(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-generate-invoice');

            return redirect('/admin/orders/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Pedido:
        $order = Order::find($request->order_id);

        //Facturación de pedido. 
        //1. El pedido debe estar en algún status que permita su facturación:
        //2. El pedido no debe pertenecer a ninguna agrupación.
        //3. No debe ser agrupación.
        if(!in_array($order->order_status, config('constants.ORDER_STATUS_INVOICED_')) || $order->order_parent || $order->is_group){
            $alert = trans('textos.pedido_no_facturable');
            return redirect()->route('orders.edit', $order->id)->with(compact('alert'));
            exit;
        }

        //Importe total del pedido:
        $order_amount = $order->getAmount();

        //Comprobamos si existen otras facturas anteriores del mismo pedido:
        $other_invoices = Invoice::select('id', 'amount', 'with_iva', 'amount_with_iva')
        ->where('order_id', $order->id)
        ->whereNull('deleted_at')
        ->whereNull('refund')
        ->where('status', '!=', config('constants.INVOICE_REFUNDED_'))
        ->get();
        //Importe ya facturado con anterioridad:
        $previous_amount = 0;
        $previous_percent = 0;
        if($other_invoices->count()){
            foreach($other_invoices as $oi){
                $previous_amount = $previous_amount + $oi->amount;
            }    
        }

        //Importe pendiente, descontadas facturas anteriores:
        $outstanding_amount = $order_amount['amount_with_discount'] - $previous_amount;
        //Porcentaje pendiente:
        $outstanding_percent = 100 - round($previous_percent, 2);

        $total_amount = 0;
        $amount_with_iva = 0;
        $advance = false;
        $advance_percent = NULL;
        $percentage_invoiced = NULL;

        //Si la factura es un avance se incluye el importe a facturar o se obtiene el porcentaje sobre el total del pedido:
        //Se considera cada avance como una previsión de facturación, por lo que se crea registro en order_previsions.
        //Facturas íntegras:
        if($request->modality == 't'){
            $total_amount = $outstanding_amount;
            //Se validarán las previsiones de facturación restantes.
            $percentage_invoiced = false;

        //Facturas parciales:
        }else{
            //Si viene declarado el porcentaje, se convierte a importe neto:
            if($request->advance_percent){
                $provisional_amount = helpers::percent($order_amount['amount_with_discount'], $request->advance_percent);
                
                //El importe obtenido por porcentaje no puede ser superior al importe pendiente:
                if($provisional_amount > $outstanding_amount){
                    $alert = trans('textos.importe_mayor_pendiente_aviso');
                    return redirect()->route('orders.edit', [$order, false])->with(compact('alert'));  
                    exit;  
                }

                $total_amount = $provisional_amount;

            //Si viene declarado el importe:
            }elseif($request->advance_amount){
                //El importe solicitado no puede ser superior al importe pendiente:
                if($request->advance_amount > $outstanding_amount){
                    $alert = trans('textos.importe_mayor_pendiente_aviso');
                    return redirect()->route('orders.edit', [$order, false])->with(compact('alert'));  
                    exit;  
                }

                $total_amount = $request->advance_amount;
            }

            //Por previsión de facturación:
            if($request->modality && is_numeric($request->modality)){
                //Obtenemos previsión por id:
                $prevision = OrderPrevisions::find($request->modality);

                if($prevision){
                    //Si se trata de la última previsión de facturación, el importe debe ser todo el pendiente:
                    $more_previsions = OrderPrevisions::select('invoice_id')
                    ->where('order_id', $prevision->order_id)
                    ->whereNull('invoice_id')
                    ->where('id', '!=', $prevision->id)
                    ->get();

                    if(!$more_previsions->count()){
                        $total_amount = $outstanding_amount;
                    }else{
                        $total_amount = helpers::percent($order_amount['amount_with_discount'], $prevision->percent);
                    }

                    $percentage_invoiced = $prevision->percent;
                }
            }
        }

        //Cálculo IVA:
        if($total_amount > 0){
            $iva_amount = helpers::percent($total_amount, $this->co_iva);
            $amount_with_iva = $total_amount + $iva_amount;
        }

        //Creamos cabecera de la factura, para poder utilizar su id:
        $invoice = new Invoice();
        $invoice->order_id = $order->id;
        $invoice->user_id = Auth::user()->id;
        $invoice->amount = $total_amount;
        $invoice->with_iva = $this->co_iva;
        $invoice->amount_with_iva = $amount_with_iva;
        $invoice->fiscal_date = date('Y-m-d');
        $invoice->status = config('constants.INVOICE_OPEN_');
        $invoice->save();

        //Marcamos el pedido como facturado:
        if($invoice && !$order->invoiced){
            $order->invoiced = true;
            $order->save();
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
            $percent = ($invoice->amount * 100) / $order_amount['amount_with_discount'];

            $new_prev = new OrderPrevisions();
            $new_prev->order_id = $order->id;
            $new_prev->year = Date('Y');
            $new_prev->month = Date('m');
            $new_prev->percent = round($percent, 2);
            $new_prev->invoice_id = $invoice->id;
            $new_prev->save();    
        }

        //Recalculando porcentaje de las previsiones de facturación:
        OrderPrevisions::recalculatePrevisionsPercent($order);

        $msg = trans('textos.factura_creada');
        return redirect()->route('invoices.edit', [$invoice->id, false])->with(compact('msg'));
    }

    /**
     * 2.1. Generar factura desde agrupación de pedidos.
     */
    public function createFromGroup(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/order-groups/'.$request->order_id.'/to-invoice')
                ->withErrors($validator)
                ->withInput();
        }

        //Pedido:
        $order = Order::find($request->order_id);

        //Facturación de agrupación. 
        //1. El pedido debe estar en algún status que permita su facturación.
        //2. Debe ser una agrupación.
        if(!in_array($order->order_status, config('constants.ORDER_GROUPS_OPENED_')) || !$order->is_group){
            $alert = trans('textos.pedido_no_facturable');
            return redirect()->route('orders.edit', $order->id)->with(compact('alert'));
            exit;
        }

        //Importe total de los pedidos seleccionados:
        $orders_amount = 0;
        foreach($request->orders as $o){
            $ord = Order::select('id', 'discount', 'invoiced')->find($o);
            $o_amount = $ord->getAmount();
            $orders_amount = $orders_amount + $o_amount['amount_with_discount'];
        }

        //Importe total de la agrupación:
        $group_amount = $order->getGroupAmount();


        //dd($request->all(), $orders_amount, $group_amount);



        //Buscamos otras facturas del mismo $group y obtenemos el importe restante pendiente:
        //$invoices = Invoice::where('og_id', $group->id)->get();










        //Cálculo IVA:
        if($orders_amount > 0){
            $iva_amount = helpers::percent($orders_amount, $this->co_iva);
            $amount_with_iva = $orders_amount + $iva_amount;
        }

        //Creamos cabecera de la factura, para poder utilizar su id:
        $invoice = new Invoice();
        $invoice->order_id = $order->id;
        $invoice->user_id = Auth::user()->id;
        $invoice->amount = $orders_amount;
        $invoice->with_iva = $this->co_iva;
        $invoice->amount_with_iva = $amount_with_iva;
        $invoice->fiscal_date = date('Y-m-d');
        $invoice->status = config('constants.INVOICE_OPEN_');
        $invoice->save();

        //11/06/22: ¿SE UTILIZAN TAMBIÉN PARA LAS AGRUPACIONES LAS PREVISIONES DE FACTURACIÓN?
        //Actualizamos los valores de la siguiente previsión no facturada:
        $next_previsions_to_invoice = OrderPrevisions::where('order_id', $order->id)
        ->whereNull('invoice_id')
        ->orderBy('year', 'ASC')
        ->orderBy('month', 'ASC')
        ->first();

        if($next_previsions_to_invoice){
            $next_previsions_to_invoice->invoice_id = $invoice->id;
            // if($percentage_invoiced){
            //     $next_previsions_to_invoice->percent = $percentage_invoiced;
            // }
            $next_previsions_to_invoice->save();
        }

        //Si existe bolsa y la suma de las facturas de la agrupación iguala o supera el importe de la bolsa, lo marcamos como facturado:
        if($order->bag_amount > 0){
            $invoices_amount = Invoice::where('order_id', $order->id)
            ->sum('amount');

            if($invoices_amount >= $order->bag_amount){
                $order->invoiced = true;
                $order->save(); 
            }
        }

        //Marcamos los pedidos seleccionados como facturados y vinculamos a la factura:
        foreach($request->orders as $o){
            Order::where('id', $o)
            ->update(['invoiced' => true]);

            $io = new InvoiceOrders();
            $io->invoice_id = $invoice->id;
            $io->order_id = $o;
            $io->save();
        }

        $msg = trans('textos.factura_creada');
        return redirect()->route('invoices.edit', [$invoice->id, false])->with(compact('msg'));
    }

    /**
     * 3. Editar factura.
     */
    public function edit(Invoice $invoice, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.factura_editar',
            'module' => $this->module,
            'slug' => 'invoices'
        ]; 

        //Pedido:
        $order = Order::find($invoice->order_id);

        //Si se trata de una agrupación, obtenemos los pedidos vinculados:
        $suborders = false;
        if($order->is_group){
            $suborders = Order::select('orders.id', 'orders.order_num', 'orders.name', 'orders.purchase_num', 'orders.budget_num', 'orders.discount', 'orders.invoiced', 'orders.order_status')
            ->join('invoice_orders', 'orders.id', '=', 'invoice_orders.order_id')
            ->where('invoice_orders.invoice_id', $invoice->id)
            ->get();
        }

        //Importe facturado:
        $invoiced = $order->getInvoicedAmount();

        //Facturas del pedido:
        $invoices = Invoice::select('id', 'amount', 'amount_with_iva')
        ->where('order_id', $order->id)
        ->get();

        //Factura de abono vinculada:
        $refund = Invoice::select('id', 'invoice_num')->where('refund', $invoice->id)->first();

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($order->customer_id); 

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        //Líneas del pedido: 
        $items = Item::where('order_id', $order->id)
        ->orderBy('concept', 'ASC')
        ->get();

        //Patrones de numeración de la factura:
        $patterns = false;
        if(!$invoice->pattern_id){
            $patterns = InvoicePatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        //Pagos:
        $payments = InvoicePayments::where('invoice_id', $invoice->id)
        ->orderBy('created_at', 'DESC')
        ->get();

        //Templates:
        $templates = [];
        if($order->provider_id == session('currentCompany')){
            $templates = InvoiceSettings::select('id', 'name', 'featured')
            ->where('company_id', $order->provider_id)
            ->where('status', 1)
            ->get();
        }

        //Template por defecto:
        $featuredTemplate = false;
        if(!$invoice->template_id){
            $featuredTemplate = InvoiceSettings::where('company_id', $invoice->provider_id)     
            ->where('featured', 1)
            ->first();

            if($featuredTemplate){
                $template = [
                    'conditions' => $featuredTemplate->conditions, 
                    'payment_method' => $featuredTemplate->payment_method,
                    'observations' => $featuredTemplate->observations
                ];
                $template = serialize($template);

                $invoice->template_id = $featuredTemplate->id;
                $invoice->template = $template;
                $invoice->save();
            }
        }

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $languages = $this->languages;

        //Cambios bloqueados: establece en función de si el pedido está facturado o no, las opciones que quedan bloqueadas.
        $permitted = true;

        $module_budgets = $this->settedModule('budgets');
        $module_products = $this->settedModule('products');
        $module_projects = $this->settedModule('projects');

        //Importe:
        if($order->bag_amount){
            $order_amount = $order->bag_amount;
        }else{
            $items = $order->items();
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
            $order_amount_without_discount = $arr_amounts['amount_without_discount'];
        } 

        //Métodos de pago:
        $payment_methods = helpers::paymentMethods();

        //Cuentas bancarias:
        $bank_accounts = [];
        $bank_featured = false;
        $bank_accounts_data = BankAccounts::select('bank_accounts.id', 'bank_accounts.account', 'bank_accounts.featured', 'banks.name', 'banks.tradename')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->where('bank_accounts.company_id', session('currentCompany'))
        ->where('bank_accounts.status', 1)
        ->orderBy('bank_accounts.featured', 'DESC')
        ->get();
        foreach($bank_accounts_data as $ba){
            $bank = $ba->tradename? $ba->tradename:$ba->name;
            $bank_accounts[$ba->id] = $bank.' - '.$ba->account;

            if($ba->featured == 1) $bank_featured = $ba->id;
        }

        return view('admin.invoices.edit')->with(compact('header', 'invoice', 'tab', 'order', 'suborders', 'invoiced', 'invoices', 'refund', 'customer', 'provider', 'items', 'patterns', 'payments', 'templates', 'featuredTemplate', 'currency', 'languages', 'permitted', 'module_budgets', 'module_products', 'module_projects', 'order_amount', 'payment_methods', 'bank_accounts', 'bank_featured'));
    }

    /**
     * 4. Actualizar factura.
     */
    public function update(Request $request, Invoice $invoice){
        //Número de factura:
        $invoice_num = false;
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('invoice', $request->pattern_id);
        } 

        //Formateando fechas:
        $fiscal_date = $request->fiscal_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->fiscal_date):$request->fiscal_date:NULL;
        $expiration_date = $request->expiration_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->expiration_date):$request->expiration_date:NULL;

        //Actualizando datos:
        if($request->pattern_id){
            if(!empty($patternNum)){
                $invoice->invoice_num = $patternNum['pattern_num'];  
                $invoice->num_cardinal = $patternNum['num']; 
            }            
            $invoice->pattern_id = $request->pattern_id;
        }

        //Campos que sólo se actualizan si la factura NO está bloqueada:
        if(!$invoice->blocked){
            $invoice->invoice_old_num = $request->invoice_old_num;
            $invoice->fiscal_date = $fiscal_date;
            $invoice->expiration_date = $expiration_date;
            $invoice->status = $request->status? $request->status:'0';
        }
        $invoice->observations = $request->observations;
        $invoice->save();

        $msg = trans('textos.factura_updated');
        return redirect()->route('invoices.edit', [$invoice, false])->with(compact('msg'));
    }

    /**
     * 5. Imprimir factura.
     */
    public function print(Invoice $invoice){
        if(!$invoice || $invoice->deleted_at){
            $alert = trans('textos.factura_no_existe');
            return redirect()->route('invoices.index')->with(compact('alert')); 
            exit;       
        }  

        $invoice_order = Order::select('orders.*', 'users.name AS Nombre', 'users.surname')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->where('orders.id', $invoice->order_id)
        ->first();

        //dd($invoice_order);

        $items = Item::where('order_id', $invoice_order->id)
        ->orderBy('concept', 'ASC')
        ->get();


        //Anticipos:
        $advance_invoices = Invoice::select('id', 'invoice_num', 'amount', 'with_iva', 'amount_with_iva')
        ->where('order_id', $invoice_order->id)
        ->get();

        //Proveedor:
        $provider = Company::find($invoice_order->provider_id);

        //Customizando CIF, ponemos espacio en blanco:
        $provider->nif = helpers::setCif($provider->nif);

        //Cliente:
        $customer = Company::find($invoice->customer_id);

        $template = $invoice->template? unserialize($invoice->template):false;

        $fechaLiteral = helpers::fechaLiteral($invoice->created_at, session()->get('locale'));

        $company_settings = session('companySettings');

        $provider_workplace = false;
        $provider_phones = false;



        $customer_workplace = false;
        $customer_responsible = false;

        //Moneda:
        $currency = $this->co_currency;

        //Actualizamos número de impresiones:
        $invoice->printed = $invoice->printed + 1;
        $invoice->save();






        //Nombre del archivo:
        $invoice_name_pdf = $invoice->invoice_num? $invoice->invoice_num:trans('textos.factura').'_'.$invoice->id;

        //Template de factura. Se pasa por variable por si se incluyen más templates.
        $invoice_template = 'pdf/invoice';

        $pdf = PDF::loadView($invoice_template, compact('invoice', 'invoice_order', 'items', 'advance_invoices', 'provider', 'customer', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'provider_phones', 'customer_workplace', 'customer_responsible', 'currency'));
        return $pdf->download($invoice_name_pdf.'.pdf'); 
    }

    /**
     * 6. Setear info para listado de facturas.
     */
    public function setInvoicesData($invoices){
        //Moneda:
        $currency = $this->co_currency;

        $arrEstados = helpers::invoiceStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        //Permisos del usuario:
        $editar = Auth::user()->can('invoices.edit');
        $eliminar = Auth::user()->can('invoices.destroy');

        //Ampliación de información de los objetos:
        if($invoices){
            foreach($invoices as $row){
                //Enviamos las fechas ya formateadas:
                $row->fechaVencimiento = Carbon::parse($row->expiration_date)->format($this->languages[session()->get('locale')][4]);

                //Fecha fiscal:
                $row->fechaFactura = Carbon::parse($row->fiscal_date)->format($this->languages[session()->get('locale')][4]);

                //Importe:
                $row->amount = $row->refund? $row->amount*(-1):$row->amount;

                //Estado de la factura:
                $row->estado = $row->status? ucfirst($arrEstados[$row->status]):'';
                $row->estados = $row->status.';'.$strEstados;

                //Factura devuelta:
                $row->refunded = in_array($row->status, config('constants.INVOICE_STATUS_REFUNDED_'))? true:false;

                //Mes y año:
                $datestamp = strtotime($row->fiscal_date); 
                $month = date("m", $datestamp);
                $month = helpers::yearMonths($month);
                $row->month = ucfirst($month);

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar;

                //Moneda:
                $row->currency = $this->co_currency->symbol;
            }  
        }

        return $invoices;
    }

    /**
     * 7. Actualizar estado de factura.
     */
    public function status(Request $request, Invoice $invoice, $status){
        if($request->ajax()){    
            $invoice->status = $status;
            $invoice->save();

            return response()->json(true);
        }
    }

    /**
     * 8. Bloquear factura.
     */
    public function block(Invoice $invoice, $return = false){
        $invoice->blocked = true; 
        $invoice->save(); 

        $msg = trans('textos.factura_bloqueada_ok');
        if($return == 'by-order'){
            return redirect()->route('invoices.by-order', $invoice->order_id)->with(compact('msg'));
        }else{
            return redirect()->route('invoices.edit', [$invoice, false])->with(compact('msg'));  
        }
    }

    /**
     * 9. Actualizar plantilla de factura.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateTemplate(Request $request, Invoice $invoice){
        if(!$invoice || $invoice->deleted_at){
            $alert = trans('textos.factura_no_existe');
            return redirect()->route('invoices.index')->with(compact('alert')); 
            exit;       
        }

        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/invoices/'.$invoice->id.'/edit/template')
                ->withErrors($validator)
                ->withInput();
        }

        //Si no existe template_id o el recibido es distinto al almacenado, guardamos los datos recibidos tal cual:
        if(!$invoice->template_id || $invoice->template_id != $request->template_id){
            //Plantilla original:
            $orig_template = InvoiceSettings::find($request->template_id);

            $template = [
                'conditions' => $orig_template->conditions, 
                'payment_method' => $orig_template->payment_method,
                'observations' => $orig_template->observations
            ];
        
        //Se mantiene el mismo template_id:
        }elseif($invoice->template_id == $request->template_id){
            $template = [
                'conditions' => $request->conditions, 
                'payment_method' => $request->payment_method,
                'observations' => $request->observations
            ];
        }
        $template = serialize($template);

        $invoice->template_id = $request->template_id;
        $invoice->template = $template;
        $invoice->save();

        $msg = trans('textos.factura_updated');
        return redirect()->route('invoices.edit', [$invoice->id, 'template'])->with(compact('msg'));
    }

    /**
     * 10. Facturas por pedido.
     */
    public function invoicesByOrder(Order $order){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.facturas_por_pedido',
            'module' => $this->module,
            'slug' => 'invoices'
        ]; 

        $customer = Company::select('name')->where('id', $order->customer_id)->first();
        $provider = Company::select('name')->where('id', $order->provider_id)->first();

        //Importe del pedido:
        if($order->bag_amount){
            $order_amount = $order->bag_amount;
            $order_amount_without_discount = false;
        }else{
            $items = $order->items();
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
            $order_amount_without_discount = $arr_amounts['amount_without_discount'];
        } 

        //Facturas:
        $invoices = Invoice::where('order_id', $order->id)
        ->orderBy('fiscal_date', 'ASC')
        ->get();    

        //Estados de factura:
        $invoice_status = helpers::invoiceStatus();

        //Moneda:
        $currency = $this->co_currency;

        $languages = $this->languages;

        return view('admin.invoices.invoices-by-order')->with(compact('header', 'order', 'customer', 'provider', 'order_amount', 'order_amount_without_discount', 'invoices', 'invoice_status', 'currency', 'languages'));
    }

    /**
     * 10.1. Facturas por agrupación.
     */
    public function invoicesByOrderGroup(Order $order){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.facturas_por_agrupacion',
            'module' => $this->module,
            'slug' => 'invoices'
        ]; 

        $customer = Company::select('name')->where('id', $order->customer_id)->first();
        $provider = Company::select('name')->where('id', $order->provider_id)->first();

        //Facturas:
        $invoices = Invoice::where('order_id', $order->id)
        ->orderBy('fiscal_date', 'ASC')
        ->get();   

        //Importe total de la agrupación: 
        $group_amount = $order->getGroupAmount();

        //Estados de factura:
        $invoice_status = helpers::invoiceStatus();

        //Moneda:
        $currency = $this->co_currency;

        $languages = $this->languages;

        return view('admin.invoices.invoices-by-order-group')->with(compact('header', 'order', 'customer', 'provider', 'invoices', 'group_amount', 'invoice_status', 'currency', 'languages'));
    }

    /**
     * 11. Cancelar factura.
     */
    public function destroy(Request $request, Invoice $invoice){
        //Comprobamos que reuna las condiciones para ser eliminada.
        /**
         * 1. La factura no debe estar bloqueda.
         */
        if($invoice->blocked == 1){
            $alert = trans('textos.accion_no_permitida');
            return redirect()->route('invoices.edit', $invoice)->with(compact('alert'));    
        }

        //Dejamos los pedidos como NO facturados:
        Order::where('id', $invoice->order_id)
        ->update(['orders.invoiced' => false]);

        //Eliminamos las previsiones de facturación asociadas a la factura:
        OrderPrevisions::where('invoice_id', $invoice->id)->delete();

        //Eliminamos la numeración customizada:
        $invoice->invoice_num = NULL;
        $invoice->num_cardinal = NULL;
        $invoice->pattern_id = NULL;
        $invoice->save();

        $invoice->delete();

        $msg = trans('textos.factura_deleted');
        if($request->return == 'by-order'){
            return redirect()->route('invoices.by-order', $invoice->order_id)->with(compact('msg'));
        }else{
            return redirect()->route('invoices.index')->with(compact('msg'));
        }
    }

    /**
     * 12. Activar - desactivar IVA en factura.
     */
    public function withIva(Request $request, Invoice $invoice, $status){
        if($request->ajax()){  
            $iva = $status == 1? $this->co_iva:NULL;

            $invoice->with_iva = $iva;
            $invoice->save();

            //Cálculo importe factura:
            $invoice->calculateAmount();
        }
    }

    /**
     * 13. Generar factura de abono.
     */
    public function refund(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-generate-refund');

            return redirect('/admin/invoices/'.$request->invoice_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Info de la factura:
        $invoice = Invoice::find($request->invoice_id);

        //Avisos:
        if(!$invoice || $invoice->deleted_at){
            $alert = trans('textos.factura_no_existe');
            return redirect()->route('invoices.index')->with(compact('alert')); 
            exit;       
        }
        //Si la factura está bloqueada, se expulsa:
        if($invoice->blocked){
            $alert = trans('textos.factura_bloqueada_warning');
            return redirect()->route('invoices.edit', $invoice)->with(compact('alert')); 
            exit;   
        }
        //El importe indicado no puede superar al de la factura.
        if($request->refund == 'p' && $request->refund_amount > $invoice->amount){
            $alert = trans('textos.factura_importe_superior_warning');
            return redirect()->route('invoices.edit', $invoice)->with(compact('alert')); 
            exit;     
        }
        
        //Importe de la factura de abono:
        if($request->refund == 'p'){    
            $refund_amount = $request->refund_amount;

            $iva_amount = helpers::percent($refund_amount, $this->co_iva);
            $refund_amount_with_iva = $refund_amount + $iva_amount;
  
            $refund_discount_amount = NULL;
        }else{
            $refund_amount = $invoice->amount;
            $refund_amount_with_iva = $invoice->amount_with_iva;
            $refund_discount_amount = $invoice->discount_amount;
        }

        //Factura de abono:
        $refund = new Invoice();
        $refund->refund = $invoice->id;
        $refund->invoice_old_num = $invoice->invoice_old_num;
        $refund->order_id = $invoice->order_id;
        $refund->amount = $refund_amount;
        $refund->with_iva = $invoice->with_iva;
        $refund->amount_with_iva = $refund_amount_with_iva;
        $refund->fiscal_date = $invoice->fiscal_date;
        $refund->expiration_date = $invoice->expiration_date;
        $refund->user_id = Auth::user()->id;
        $refund->observations = $request->concept;
        $refund->template_id = $invoice->template_id;
        $refund->template = $invoice->template;
        $refund->save();
                
        //Al generarse una factura de abono, sólo si el abono es íntegro, el pedido original vuelve a estado de pendiente de facturación y la factura a estado de devuelta.
        //En caso de tratarse de una agrupación, los pedidos de anidados que se encuentren en la factura también a estado sin facturar.
        if($request->refund == 't'){
            $order = Order::select('id', 'is_group', 'invoiced')
            ->find($invoice->order_id);

            $order->invoiced = false;
            $order->save();

            if($order->is_group){
                Order::join('invoice_orders', 'orders.id', '=', 'invoice_orders.order_id')
                ->where('orders.order_parent', $order->id)
                ->where('invoice_orders.invoice_id', $invoice->id)
                ->update(['orders.invoiced' => false]);
            }
        }

        //Actualizando factura original:
        $invoice->status = config('constants.INVOICE_REFUNDED_');
        $invoice->save();

        //Actualizando previsiones de facturación: la desvinculamos de la factura y eliminamos el porcentaje.
        OrderPrevisions::where('invoice_id', $invoice->id)
        ->update(['percent' => NULL, 'invoice_id' => NULL]);

        $msg = trans('textos.factura_abono_creada');
        return redirect()->route('invoices.edit', $refund->id)->with(compact('msg'));
    }

    /**
     * 14. Eliminar pedido de factura de agrupación.
     */
    public function removeFromInvoice(Invoice $invoice, Order $order){
        //Si la factura está bloqueada, se expulsa:
        if($invoice->blocked){
            $alert = trans('textos.factura_bloqueada_warning');
            return redirect()->route('invoices.edit', $invoice)->with(compact('alert')); 
            exit;   
        }

        //Eliminamos pedido de la factura de agrupación:
        InvoiceOrders::where('invoice_id', $invoice->id)
        ->where('order_id', $order->id)
        ->delete();

        //Desmarcamos el pedido como facturado.
        $order->invoiced = false;
        $order->save();

        //Recalculando importe total de la agrupación, con los pedidos restantes en la factura:
        $suborders = Order::select('orders.id', 'orders.order_num', 'orders.name', 'orders.purchase_num', 'orders.budget_num', 'orders.discount', 'orders.invoiced', 'orders.order_status')
            ->join('invoice_orders', 'orders.id', '=', 'invoice_orders.order_id')
            ->where('invoice_orders.invoice_id', $invoice->id)
            ->get();

        $invoice_amount = 0;
        $amount_with_iva = 0;
        foreach($suborders as $so){
            $amount = $so->getAmount();
            $invoice_amount = $invoice_amount + $amount['amount_with_discount'];
        }

        $invoice->amount = $invoice_amount;

        //Cálculo IVA:
        if($invoice_amount > 0 && $invoice->with_iva){
            $iva_amount = helpers::percent($invoice_amount, $this->co_iva);
            $amount_with_iva = $invoice_amount + $iva_amount;
        }
        $invoice->amount_with_iva = $amount_with_iva;
        $invoice->save();      

        $msg = trans('textos.factura_agrupacion_pedido_eliminado');
        return redirect()->route('invoices.edit', $invoice)->with(compact('msg'));
    }
}

