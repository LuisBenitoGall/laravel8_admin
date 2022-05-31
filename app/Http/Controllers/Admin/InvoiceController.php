<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Notifications\PurchaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\BudgetOrderSharedFields;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Invoice;
use App\Models\InvoicePatterns;
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
     * 3. Editar factura.
     * 4. Actualizar factura.
     * 5. Imprimir factura.
     * 6. Setear info para listado de facturas.
     * 7. Actualizar estado de factura.
     * 8. Bloquear factura.
     * 9. Actualizar plantilla de presupuesto.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        //IVA:
        $this->co_iva = CompanySettings::co_iva();

        $this->middleware('module_setted:accounting');
        $this->middleware('invoice_get', ['only' => ['edit', 'update', 'print', 'block', 'updateTemplate']]);
        $this->middleware('invoice_modality', ['only' => ['createFromOrder']]);
        $this->middleware('order_post', ['only' => ['createFromOrder']]);
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

        //Validación template:
        }elseif(isset($data['submit_update_template'])){
            return Validator::make($data, [
                'template_id' => 'required|numeric'
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
        ->join('budget_order_shared_fields', 'invoices.shared_id', '=', 'budget_order_shared_fields.id')
        ->leftJoin('companies', 'budget_order_shared_fields.customer_id', '=', 'companies.id')
        ->leftJoin('cost_centers', 'budget_order_shared_fields.provider_cost_center_id', '=', 'cost_centers.id')
        ->leftJoin('orders', 'budget_order_shared_fields.order_id', '=', 'orders.id')
        ->leftJoin('users', 'budget_order_shared_fields.provider_manager_id', '=', 'users.id')
        ->whereYear('invoices.fiscal_date', $year)
        ->whereNull('invoices.deleted_at');
        //Filtro cliente:
        $providerId = $provider? $provider:session('currentCompany');
        $query->when($customer, function($q) use($customer, $providerId){
            return $q->where('budget_order_shared_fields.customer_id', $customer)->where('budget_order_shared_fields.provider_id', $providerId);
        });
        //Filtro proveedor: 
        $query->when($provider, function($q) use($provider){
            return $q->where('budget_order_shared_fields.provider_id', $provider);
        }); 
        //Filtro por defecto:
        $currentCompany = session('currentCompany');
        $query->when(!$customer && !$provider, function($q) use($currentCompany){
            return $q->where('budget_order_shared_fields.provider_id', $currentCompany);
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

        //Presupuesto - pedido:
        $shared = BudgetOrderSharedFields::where('order_id', $order->id)->first();

        //Facturación de pedido. El pedido debe estar en algún status que permita su facturación:
        if(!in_array($order->status, config('constants.ORDER_STATUS_INVOICED_'))){
            $alert = trans('textos.pedido_no_facturable');
            return redirect()->route('orders.edit', $order->id)->with(compact('alert'));
            exit;
        }

        //Importe total del pedido:
        $order_amount = $shared->getAmount();

        //Comprobamos si existen otras facturas anteriores del mismo pedido:
        $other_invoices = Invoice::select('id', 'amount', 'with_iva', 'amount_with_iva', 'percentage_invoiced')
        ->where('shared_id', $shared->id)
        ->whereNull('deleted_at')
        ->get();
        //Importe ya facturado con anterioridad:
        $previous_amount = 0;
        $previous_percent = 0;
        if($other_invoices->count()){
            foreach($other_invoices as $oi){
                $previous_amount = $previous_amount + $oi->amount;
                $previous_percent = $previous_percent + $oi->percentage_invoiced;    
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
        $percentage_invoiced = 0;

        //Creamos cabecera de la factura, para poder utilizar su id:
        $invoice = new Invoice();
        $invoice->shared_id = $shared->id;
        $invoice->user_id = Auth::user()->id;
        $invoice->save();

        //dd($request->all(), $order, $order_amount, $previous_amount, $previous_percent, $outstanding_amount, $outstanding_percent, $this->co_iva, $invoice);

        //Si la factura es un avance se incluye el importe a facturar o se obtiene el porcentaje sobre el total del pedido:
        //Se considera cada avance como una previsión de facturación, por lo que se crea registro en order_previsions.
        //Facturas íntegras:
        if($request->modality == 't'){
            $total_amount = $outstanding_amount;
            $percentage_invoiced = $outstanding_percent;

            //Actualizamos los valores de las previsiones que no estuviesen facturadas:
            OrderPrevisions::where('order_id', $order->id)
            ->where('invoiced', 0)
            ->update(['percent' => $outstanding_percent, 'invoiced' => 1]);

        //Facturas parciales:
        }else{
            
        }

        //Cálculo IVA:
        if($total_amount > 0){
            $iva_amount = helpers::percent($total_amount, $this->co_iva);
            $amount_with_iva = $total_amount + $iva_amount;
        }












        

        //Completamos factura:
        
        $invoice->amount = $total_amount;
        $invoice->with_iva = $this->co_iva;
        $invoice->amount_with_iva = $amount_with_iva;
        $invoice->percentage_invoiced = $percentage_invoiced;
        $invoice->fiscal_date = date('Y-m-d');
        $invoice->status = config('constants.INVOICE_OPEN_');
        $invoice->save();

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

        $shared = BudgetOrderSharedFields::find($invoice->shared_id);

        //Pedido:
        $order = Order::find($shared->order_id);

        //Importe facturado:
        $invoiced = $shared->getInvoicedAmount();

        //Facturas del pedido:
        $invoices = Invoice::select('id', 'amount', 'amount_with_iva')
        ->where('shared_id', $shared->id)
        ->get();

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($shared->customer_id); 

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($shared->provider_id);

        //Líneas del pedido: 
        $items = Item::where('budget_order_id', $shared->id)
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

        //Templates:
        $templates = [];
        if($shared->provider_id == session('currentCompany')){
            $templates = InvoiceSettings::select('id', 'name', 'featured')
            ->where('company_id', $shared->provider_id)
            ->where('status', 1)
            ->get();
        }

        //Template por defecto:
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
        if($shared->free_amount){
            $order_amount = $shared->free_amount;
        }else{
            $items = $shared->getBudgetOrderItems();
            $arr_amounts = $shared->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
            $order_amount_without_discount = $arr_amounts['amount_without_discount'];
        } 

        return view('admin.invoices.edit')->with(compact('header', 'invoice', 'tab', 'shared', 'order', 'invoiced', 'invoices', 'customer', 'provider', 'items', 'patterns', 'templates', 'featuredTemplate', 'currency', 'languages', 'permitted', 'module_budgets', 'module_products', 'module_projects', 'order_amount', 'order_amount_without_discount'));
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
    public function block(Invoice $invoice){
        $invoice->blocked = true; 
        $invoice->save(); 

        $msg = trans('textos.factura_bloqueada_ok');
        return redirect()->route('invoices.edit', [$invoice, false])->with(compact('msg'));  
    }

    /**
     * 9. Actualizar plantilla de presupuesto.
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
}
