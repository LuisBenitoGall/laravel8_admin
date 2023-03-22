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
use Yajra\DataTables\DataTables;
use File;
use PDF;
use Carbon\Carbon;

//Models:
use App\Models\AccountingConcept;
use App\Models\AccountingOption;
use App\Models\BankAccounts;
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Invoice;
//use App\Models\InvoiceItems;
use App\Models\InvoiceOrders;
use App\Models\InvoicePatterns;
use App\Models\InvoiceSettings;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderPrevisions;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Phone;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectPurchases;
use App\Models\User;
use App\Models\Workplace;

class InvoiceController extends Controller{
    /**
     * 1. Facturas de clientes.
     * 1.1. Listado de facturas para DataTable.
     * 1.2. Listado de facturas por cliente o proveedor para DataTable.
     * 2. Generar factura desde pedido.
     * 2.1. Generar factura desde agrupación de pedidos.
     * 2.2. Introducir factura de proveedor.
     * 2.3. Guardar factura de proveedor.
     * 3. Editar factura.
     * 3.1. Editar factura por parte de cliente.
     * 3.2. Mostrar factura.
     * 3.3. Recalcular importe factura a partir de sus líneas
     * 4. Actualizar factura.
     * 5. Imprimir factura.
     * 6. Setear info para listado de facturas.
     * 7. Actualizar estado de factura.
     * 8. Bloquear factura.
     * 8.1. Desbloquear factura.
     * 9. Actualizar plantilla de factura.
     * 10. Facturas por pedido.
     * 10.1. Facturas por agrupación.
     * 11. Cancelar factura.
     * 12. Activar - desactivar IVA en factura.
     * 13. Generar factura de abono.
     * 14. Eliminar pedido de factura de agrupación.
     * 15. Opciones contables.
     * 16. Control de facturación.
     * 16.1. Control de facturación para DataTable.
     * 17. Eliminar documento.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:accounting');
        $this->middleware('invoice_customer_get', ['only' => ['editPurchase']]);
        $this->middleware('invoice_get', ['only' => ['edit', 'block', 'unlock', 'updateTemplate', 'destroy']]);
        $this->middleware('invoice_update_get', ['only' => ['update', 'print']]);
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
                'refund_amount.required_if' => trans('textos.importe_obligatorio')
            ]);

        //Validación factura desde proveedor:
        }elseif(isset($data['submit_from_provider'])){
            return Validator::make($data, [
                'provider_id' => 'required|numeric',
                'invoice_old_num' => 'required'
            ]);
        }
    }

    /**
     * 1. Facturas por cliente o proveedor.
     */
    public function index($year = false, $month = false, $company = false){
        $customer_id = false;
        $provider_id = false;
        $invoicesType = 'facturas';

        if($company){
            $p = explode('-', $company);

            //Ventas:
            if($p[0] == 'c'){
                $provider_id = session('currentCompany');
                $customer_id = $p[1];

            //Compras:
            }elseif($p[0] == 'p'){
                $customer_id = session('currentCompany');
                $provider_id = $p[1];
            }
        }

        if($customer_id && $customer_id == session('currentCompany')){
            $invoicesType = 'facturas_compras';
        }elseif($provider_id && $provider_id == session('currentCompany')){
            $invoicesType = 'facturas_ventas';
        }   
        
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$invoicesType,
            'module' => $this->module,
            'slug' => 'invoices'
        ];  

        if(!$year) $year = Date('Y');

        $customer = $customer_id && $customer_id != 'all'? Company::find($customer_id):false;
        $provider = $provider_id && $provider_id != 'all'? Company::find($provider_id):false;

        $customers = false;
        $providers = false;

        //Moneda:
        $currency = CompanySettings::co_currency(0); 

        //Comprobamos si la empresa hace gestión de clientes:
        if(session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }

        //Comprobamos si la empresa hace gestión de proveedores:
        if(session('companySettings')['providers_management'] == 1){
            $providers = CustomerProviders::getCompanyProviders(session('currentCompany'));
        }

        return view('admin.invoices.index')->with(compact('header', 'invoicesType', 'year', 'month', 'customer_id', 'provider_id', 'customer', 'provider', 'customers', 'providers', 'currency'));
    }

    /**
     * 1.1. Listado de facturas para DataTable.
     */
    public function getInvoicesBy(Request $request){
        $currentCompany = session('currentCompany');

        //Si viene declarado el número de factura redirigimos:
        if($request->invoice_id){
            $invoiceId = $request->invoice_id;

            //Comprobamos si el valor recibido corresponde a id, invoice_num o old_invoice:
            $invoice = Invoice::select('invoices.id')
            ->join('orders', 'invoices.order_id', '=', 'orders.id')
            ->where('invoices.id', '>=', 1)
            ->where(function($query) use($currentCompany){
                $query->where('orders.customer_id', $currentCompany)
                ->orWhere('orders.provider_id', $currentCompany);
            })
            ->where(function($query2) use ($invoiceId){
                $query2->where('invoices.id', $invoiceId)
                ->orWhere('invoices.invoice_num', $invoiceId)
                ->orWhere('invoices.invoice_old_num', $invoiceId);
            })
            // ->where(function($query1) use ($currentCompany){
            //     $query1->where('customer_id', $currentCompany)
            //     ->orWhere('provider_id', $currentCompany);
            // })
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

        $company = $request->customer_id? 'c-'.$request->customer_id:'p-'.$request->provider_id;

        // Se comprueba que se elija un mes valido
        $month = $request->month && $request->month >= 0 && $request->month < 13 ? $request->month : 0;

        return redirect()->route('invoices.index', [$request->year, $month, $company]);
    }

    /**
     * 1.2. Listado de facturas por cliente o proveedor para DataTable.
     */
    public function getInvoicesDT($year = false, $customer = false, $provider = false, $month = false){
        //Ambito de las facturas:
        $environment = $provider == session('currentCompany') || $customer == 'all'? 'sales':'purchases';

        //Campo de búsqueda de la empresa: proveedor o cliente.
        $company_field = $provider == session('currentCompany') || $customer == 'all'? 'customer':'provider';

        $data = Invoice::select('invoices.*', 'orders.id AS orderId', 'orders.order_num', 'orders.order_old_num', 'companies.id AS companyId', 'companies.name', 'companies.tradename', 'cost_centers.name AS costCenter', 'users.name AS Nombre', 'users.surname', 'payment_methods.name AS payment_method')
        ->leftJoin('orders', 'invoices.order_id', '=', 'orders.id')
        ->leftJoin('companies', 'orders.'.$company_field.'_id', '=', 'companies.id')
        ->leftJoin('cost_centers', 'orders.'.$company_field.'_cost_center_id', '=', 'cost_centers.id')
        ->leftJoin('users', 'orders.'.$company_field.'_manager_id', '=', 'users.id')
        ->leftJoin('payment_methods', 'invoices.payment_method_id', '=', 'payment_methods.id')
        ->where(function ($query) use($year){
            $query->whereYear('invoices.fiscal_date', $year)
            ->orWhereNull('invoices.fiscal_date');
        })
        ->whereNull('invoices.deleted_at')
        ->when($customer && $provider, function($q) use($customer, $provider){
            if(is_numeric($customer) && is_numeric($provider)){
                return $q->where('orders.customer_id', $customer)
                ->where('orders.provider_id', $provider);

            }elseif($customer == 'all'){
                return $q->where('orders.provider_id', $provider);

            }elseif($provider == 'all'){
                return $q->where('orders.customer_id', $customer);
            }
        })
        ->when($month, function($query) use ($month) {
            return $query->whereMonth('invoices.fiscal_date', $month);
        })
        ->orderBy('invoices.created_at', 'DESC');

        // $query->when($customer && is_int($customer), function($q) use($customer){
        //     return $q->where('orders.customer_id', $customer);
        // });
        // //Filtro proveedor: 
        // $query->when($provider && is_int($provider), function($q) use($provider){
        //     return $q->where('orders.provider_id', $provider);
        // });
        
        $estados = helpers::invoiceStatus();

        //Moneda:
        $currency = CompanySettings::co_currency(0);   

        return DataTables::of($data) 
        ->addColumn('invoice_num', function($row) use($environment){
            $route = $environment == 'sales'? 'edit':'edit-purchase';
            $num = $row->invoice_num? $row->invoice_num:($row->invoice_old_num? $row->invoice_old_num:$row->id);
            return '<a href="'.route('invoices.'.$route, $row).'">'.$num.'</a>'; 
        })
        ->addColumn('company', function($row) use($environment){
            return $row->name;
        })
        ->addColumn('order', function($row){
            $num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->orderId);
            return '<a target="_blank" href="'.route('orders.edit', $row->orderId).'">'.$num.'</a>';
        })
        ->addColumn('manager', function($row) use($environment){
            return $row->Nombre.' '.$row->surname;
        })
        ->addColumn('fiscal_date', function($row){
            return $row->fiscal_date? Carbon::parse($row->fiscal_date)->format($this->languages[session()->get('locale')][4]):'';
        })
        ->addColumn('expiration_date', function($row){
            return Carbon::parse($row->expiration_date)->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('amount', function($row) use($currency){
            $amount = $row->refund? $row->amount * -1:$row->amount;
            return $amount;   
        })
        ->addColumn('amount_with_iva', function($row) use($currency){
            $amount_with_iva = $row->refund? $row->amount_with_iva * -1:$row->amount_with_iva;
            return $amount_with_iva;
        })
        ->addColumn('payment_method', function($row){
            return $row->payment_method;
        })
        ->addColumn('month', function($row){
            $datestamp = strtotime($row->fiscal_date); 
            $month = date("m", $datestamp);
            $month = helpers::yearMonths($month);
            return ucfirst($month);
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.invoices.partials.dt-status', compact('row', 'estados'));    
        })
        ->addColumn('acciones', function($row) use($estados, $environment){
            $manage_status = true;
            return view('admin.invoices.partials.dt-actions', compact('row', 'estados', 'environment', 'manage_status'));    
        })
        ->rawColumns(['invoice_num', 'order'])
        ->make(true);


        //Campo de búsqueda de la empresa: proveedor o cliente.
        // $company_field = $provider? 'provider_id':'customer_id';

        // $query = Invoice::select('invoices.*', 'companies.name', 'companies.tradename', 'cost_centers.name AS costCenter', 'users.name AS Nombre', 'users.surname')
        // ->leftJoin('orders', 'invoices.order_id', '=', 'orders.id')
        // ->leftJoin('companies', 'orders.'.$company_field, '=', 'companies.id')
        // ->leftJoin('cost_centers', 'orders.provider_cost_center_id', '=', 'cost_centers.id')
        // ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        // ->whereYear('invoices.fiscal_date', $year)
        // ->whereNull('invoices.deleted_at');
        // //Filtro cliente:
        // $providerId = $provider? $provider:session('currentCompany');
        // $query->when($customer, function($q) use($customer, $providerId){
        //     return $q->where('orders.customer_id', $customer)->where('orders.provider_id', $providerId);
        // });
        // //Filtro proveedor: 
        // $query->when($provider, function($q) use($provider){
        //     return $q->where('orders.provider_id', $provider);
        // }); 
        // //Filtro por defecto:
        // $currentCompany = session('currentCompany');
        // $query->when(!$customer && !$provider, function($q) use($currentCompany){
        //     return $q->where('orders.provider_id', $currentCompany);
        // });
        // $invoices = $query->orderBy('invoices.created_at', 'DESC'); 

        

        //Ampliación de información de los objetos:
        //$this->setInvoicesData($invoices, $environment);
        //return datatables($data)->toJson();
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
        $order_amount = $order->getAmount(true, true);

        //Comprobamos si existen otras facturas anteriores del mismo pedido:
        $other_invoices = Invoice::select('id', 'amount', 'with_iva', 'amount_with_iva')
        ->where('order_id', $order->id)
        ->whereNull('deleted_at')
        ->whereNull('refund')
        ->where('status', '!=', config('constants.INVOICE_REFUNDED_'))
        ->get();
        //Importe facturado con anterioridad:
        $previous_amount = 0;
        //Porcentaje facturado con anterioridad:
        $previous_percent = 0;
        if($other_invoices->count()){
            foreach($other_invoices as $oi){
                $previous_amount = $previous_amount + $oi->amount;

                $pct = ($oi->amount * 100) / $order_amount['total_amount'];
                $previous_percent = $previous_percent + $pct;
            }    
        }

        //Importe pendiente, descontadas facturas anteriores:
        $outstanding_amount = $order_amount['total_amount'] - $previous_amount;
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

            //Calculamos importe con IVA a partir del importe restante.
            $amount_with_iva = ($total_amount * $order_amount['invoice_amount']) / $order_amount['total_amount'];

            //Factura íntrega deja 0% por facturar:
            $outstanding_percent = 0;

        //Facturas parciales:
        }else{
            //Si viene declarado el porcentaje, se convierte a importe neto:
            if($request->advance_percent){
                $provisional_amount = helpers::percent($order_amount['total_amount'], $request->advance_percent);
                
                //El importe obtenido por porcentaje no puede ser superior al importe pendiente:
                if($provisional_amount > $outstanding_amount){
                    $alert = trans('textos.importe_mayor_pendiente_aviso');
                    return redirect()->route('orders.edit', [$order, false])->with(compact('alert'));  
                    exit;  

                //Obtenemos el importe con IVA:
                }else{
                    $amount_with_iva = helpers::percent($order_amount['invoice_amount'], $request->advance_percent);
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

                //Obtenemos el importe con IVA:
                $amount_with_iva = ($order_amount['invoice_amount'] * $request->advance_amount) / $order_amount['total_amount'];
            }

            //Por previsión de facturación:
            if($request->modality && is_numeric($request->modality)){
                //Obtenemos previsión por id:
                $prevision = OrderPrevisions::find($request->modality);

                if($prevision){

                    

                    //Si se trata de la última previsión de facturación y no queda porcentaje por establecer previsión, el importe debe ser todo el pendiente:
                    $more_previsions = OrderPrevisions::select('invoice_id')
                    ->where('order_id', $prevision->order_id)
                    ->whereNull('invoice_id')
                    ->where('id', '!=', $prevision->id)
                    ->get();

                    //Porcentaje pendiente de facturación. Si el porcentaje de la previsión + los porcentajes ya facturados superan el 100%, entonces el porcentaje pendiente es la diferencia entre el total y lo ya facturado.
                    $percent_to_invoice = $previous_percent + $prevision->percent > 100? 100 - $previous_percent:$prevision->percent;

                    //Importe de la factura obteniendo el porcentaje indicado sobre el total del importe del pedido.
                    $total_amount = helpers::percent($order_amount['total_amount'], $percent_to_invoice);
                    //Porcentaje facturado:
                    $percentage_invoiced = $percent_to_invoice;

                    //Importe con IVA:
                    $amount_with_iva = helpers::percent($order_amount['invoice_amount'], $percent_to_invoice);

                    //dd($total_amount, $amount_with_iva, $prevision, $request->all(), $order_amount, $previous_amount, $previous_percent, $outstanding_amount, $outstanding_percent);
                }
            }
        }

        //Cálculo IVA:
        // if($total_amount > 0){
        //     $iva_amount = helpers::percent($total_amount, $this->co_iva);
        //     $amount_with_iva = $total_amount + $iva_amount;
        // }
        
        //Cuenta bancaria:
        $bank_account_ = BankAccounts::select('id')
        ->where('company_id', $order->provider_id)
        ->where('featured', 1)
        ->where('status', 1)
        ->first();
        $bank_account = $bank_account_? $bank_account_->id:NULL;

        //Creamos cabecera de la factura, para poder utilizar su id:
        $invoice = new Invoice();
        $invoice->order_id = $order->id;
        $invoice->user_id = Auth::user()->id;
        $invoice->amount = $total_amount;
        $invoice->with_iva = CompanySettings::co_iva();
        $invoice->amount_with_iva = $amount_with_iva;
        $invoice->fiscal_date = date('Y-m-d');
        $invoice->bank_account_id = $bank_account;
        $invoice->status = config('constants.INVOICE_OPEN_');
        $invoice->save();

        //Marcamos el pedido como facturado:
        if($invoice && !$order->invoiced){
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
            $new_prev->percent = round($percent, 2);
            $new_prev->invoice_id = $invoice->id;
            $new_prev->save();    
        }

        //Recalculando porcentaje de las previsiones de facturación. Se considera que con un porcentaje por facturar por debajo del 0.1%, ya no queda nada por facturar.
        $all_invoiced = $outstanding_percent < 0.1? true:false;
        OrderPrevisions::recalculatePrevisionsPercent($order, $all_invoiced);

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
        $invoices_amount = 0;
        foreach($request->orders as $o){
            $ord = Order::select('id', 'discount', 'invoiced')->find($o);
            $o_amount = $ord->getAmount();
            $orders_amount = $orders_amount + $o_amount['total_amount'];
            $invoices_amount = $invoices_amount + $o_amount['invoice_amount'];
        }

        //Importe total de la agrupación:
        $group_amount = $order->getGroupAmount();





        //Buscamos otras facturas del mismo $group y obtenemos el importe restante pendiente:
        //$invoices = Invoice::where('og_id', $group->id)->get();










        //Cálculo IVA:
        // if($orders_amount > 0){
        //     $iva_amount = helpers::percent($orders_amount, $this->co_iva);
        //     $amount_with_iva = $orders_amount + $iva_amount;
        // }

        //Creamos cabecera de la factura, para poder utilizar su id:
        $invoice = new Invoice();
        $invoice->order_id = $order->id;
        $invoice->user_id = Auth::user()->id;
        $invoice->amount = $orders_amount;
        $invoice->with_iva = CompanySettings::co_iva();
        $invoice->amount_with_iva = $invoices_amount;
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
     * 2.2. Introducir factura de proveedor.
     */
    public function createFromProvider($project_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.factura_compra_introducir',
            'module' => $this->module,
            'slug' => 'invoices'
        ];  

        //Comprobamos que el proyecto pertenezca a la empresa en session:
        if($project_id){
            $project = Project::select('company_id')->find($project_id);
            if($project->company_id != session('currentCompany')){
                $alert = trans('textos.no_permisos_accion');   
                return redirect()->route('projects.index')->with(compact('alert'));
                exit;
            }
        } 

        $providers = Company::join('customer_providers', 'companies.id', '=', 'customer_providers.provider_id')
        ->where('customer_providers.customer_id', session('currentCompany'))
        ->where('customer_providers.status', 1)
        ->where('companies.id', '!=', session('currentCompany'))
        ->pluck('companies.name', 'companies.id')
        ->toArray();

        $languages = $this->languages;

        $currency = CompanySettings::co_currency(0);

        return view('admin.invoices.invoice-by-provider')->with(compact('header', 'project_id', 'providers', 'languages', 'currency'));          
    }

    /**
     * 2.3. Guardar factura de proveedor.
     */
    public function storeFromProvider(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/invoices/create-from-provider')
                ->withErrors($validator)
                ->withInput();
        }

        //Formateando fechas:
        $fiscal_date = $request->fiscal_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->fiscal_date):$request->fiscal_date:NULL;
        $expiration_date = $request->expiration_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->expiration_date):$request->expiration_date:NULL;

        //Comprobamos si la fecha fiscal es válida, que el mes y año no estén cerrados.
        if($fiscal_date){
            $no_valid = AccountingOption::validInvoiceDate($fiscal_date, 'p', session('currentCompany'));  
            if($no_valid){
                $alert = trans('textos.cierre_mensual_compras_aviso');
                return redirect()->route('invoices.create-from-provider')->with(compact('alert'));     
            }
        }

        //Creamos pedido si no existe. Seteamos la variable $add_items para indicar si se deben introducir líneas del pedido o no.
        if(!$request->order_id){
            $order = new Order();
            $order->is_order = 1;
            $order->from_customer = 1;      //Indicamos que es factura generada por el cliente.
            $order->customer_id = session('currentCompany');
            $order->provider_id = $request->provider_id;
            $order->order_status = config('constants.ORDER_FINISHED_');     //Damos el pedido por finalizado.
            $order->invoiced = 1;
            $order->save();

            //Generamos una línea de pedido por el importe de la factura:
            // $item = new Item();
            // $item->order_id = $order->id;
            // $item->budget_sort = 1;
            // $item->concept = trans('textos.pedido_linea');
            // $item->qty = 1;
            // $item->price = $request->amount;
            // $item->iva = $request->iva;
            // $item->invoiced = NOW();
            // $item->active = 1;
            // $item->user_id = Auth::user()->id;
            // $item->save();
            

            $order_amount = 0;
            $invoice_amount = 0;

        }else{
            $order = Order::select('id', 'invoiced')->find($request->order_id);
            $order->invoiced = 1;
            $order->save();

            //Si existe el pedido, incluimos importe en la factura:
            $arr_amounts = $order->getAmount();    
            $order_amount = $arr_amounts['total_amount'];
            $invoice_amount = $arr_amounts['invoice_amount'];
        }

        //Descontando IRPF:
        if($request->irpf > 0){
            $desc_irpf = ($request->irpf * $order_amount) / 100;
            $invoice_amount = $invoice_amount - $desc_irpf;
        }

        //Subir documento:
        $doc = $this->loadFile($request, 'invoices');

        //Creamos factura:
        $inv = new Invoice();
        $inv->invoice_old_num = $request->invoice_old_num;
        $inv->order_id = $order->id;
        $inv->amount = $order_amount;
        $inv->with_iva = config('constants.IVA_DEFAULT_');
        $inv->amount_with_iva = $invoice_amount;
        $inv->irpf = $request->irpf;
        $inv->fiscal_date = $fiscal_date;
        $inv->expiration_date = $expiration_date;
        $inv->user_id = Auth::user()->id;
        $inv->customer_observations = $request->customer_observations;
        $inv->doc = $doc;
        $inv->status = $request->status? $request->status:config('constants.INVOICE_OPEN_'); 
        $inv->blocked = 0;      //Por defecto mantenemos desbloqueadas las facturas así generadas.  
        $inv->save();

        //Vinculamos pedido a proyecto:
        if($request->project_id && $request->project_id > 0){
            ProjectPurchases::firstOrCreate(['project_id' => $request->project_id, 'order_id' => $order->id]);
        }

        $msg = trans('textos.factura_creada');
        return redirect()->route('invoices.edit-purchase', [$inv->id, false])->with(compact('msg'));       
    }

    /**
     * 3. Editar factura.
     */
    public function edit(Invoice $invoice, $tab = false){
        //Pedido:
        $order = Order::find($invoice->order_id);

        //Redirigiendo a factura de compra:
        if($order->customer_id == session('currentCompany')){
            return redirect()->route('invoices.edit-purchase', [$invoice, false]);  
            exit;
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.factura_editar',
            'module' => $this->module,
            'slug' => 'invoices'
        ]; 

        //Recalculando importe factura por sus líneas:
        //$this->recalculateInvoiceAmount($invoice);

        //Entorno: venta.
        $is_sale = true;

        

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

        //Configuraciones de cliente. Es relación obligatoria, de modo que en caso de no existir debe crearse.
        $customer_settings = CustomerProviders::where('customer_id', $order->customer_id)
        ->where('provider_id', $order->provider_id)
        ->first();
        if(!$customer_settings){
            $customer_settings = new CustomerProviders();  
            $customer_settings->customer_id = $order->customer_id;
            $customer_settings->provider_id = $order->provider_id;
            $customer_settings->status = 1;
            $customer_settings->save();
        }

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        //Cuenta del proveedor:
        $provider_account = CompanyAccounts::where('company_id', $provider->id)
        ->where('status', 1)
        ->first();

        //Configuraciones de proveedor:
        $provider_settings = CustomerProviders::select('accounting_concept_id', 'pay_after_days', 'payday', 'payment_method_id')
        ->where('customer_id', $customer->id)
        ->where('provider_id', $provider->id)
        ->first();

        //Líneas del pedido: 
        $items = Item::where('order_id', $order->id)
        ->orderBy('concept', 'ASC')
        ->get();

        //Productos. Activos y aptos para venta:
        $products = Product::companyProducts($order->provider_id, $order->customer_id, true, true);

        //Patrones de numeración de la factura:
        $patterns = false;
        if(!$invoice->pattern_id){
            $patterns = InvoicePatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        //Pagos:
        $payments = Payment::paymentsByInvoice($invoice->id);

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

            $order_amount = $arr_amounts['total_amount'];
            $order_amount_without_discount = $arr_amounts['total_amount_without_discounts'];
        } 

        //Métodos de pago:
        //$payment_methods = helpers::paymentMethods();
        $payment_methods = PaymentMethod::where('company_id', $customer->id)
        ->pluck('name', 'id')
        ->toArray();

        //Cuentas bancarias cliente:
        $bank_accounts = [];
        $bank_featured = false;
        $bank_accounts_data = BankAccounts::getBankAccounts($customer->id);
        foreach($bank_accounts_data as $ba){
            $bank = $ba->tradename? $ba->tradename:$ba->name;
            $bank_accounts[$ba->id] = $bank.' - '.$ba->account;

            if($ba->featured == 1) $bank_featured = $ba->id;
        }

        //Cuentas bancarias proveedor:
        $provider_bank_accounts = [];
        $provider_bank_featured = false;
        $provider_bank_accounts_data = BankAccounts::getBankAccounts($provider->id);
        foreach($provider_bank_accounts_data as $pba){
            $bank = $pba->tradename? $pba->tradename:$pba->name;
            $provider_bank_accounts[$pba->id] = $bank.' - '.$pba->account;

            if($pba->featured == 1) $provider_bank_featured = $pba->id;
        }

        return view('admin.invoices.edit')->with(compact('header', 'invoice', 'tab', 'is_sale', 'order', 'suborders', 'invoiced', 'invoices', 'refund', 'customer', 'customer_settings', 'provider', 'provider_account', 'provider_settings', 'items', 'products', 'patterns', 'payments', 'templates', 'featuredTemplate', 'currency', 'languages', 'permitted', 'module_budgets', 'module_products', 'module_projects', 'order_amount', 'payment_methods', 'bank_accounts', 'bank_featured', 'provider_bank_accounts', 'provider_bank_featured'));
    }

    /**
     * 3.1. Editar factura por parte de cliente.
     */
    public function editPurchase(Invoice $invoice, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.factura_compra_editar',
            'module' => $this->module,
            'slug' => 'invoices'
        ]; 

        //Pedido:
        $order = Order::select('id', 'customer_id', 'provider_id', 'bag_amount', 'from_customer')
        ->find($invoice->order_id);

        //Recalculando importe factura por sus líneas:
        if($order->from_customer == 1){
            $this->recalculateInvoiceAmount($invoice, $order);
        }
        
        //Entorno: compra.
        $is_sale = false;

        //Importe facturado:
        $invoiced = $order->getInvoicedAmount();

        //Facturas del pedido:
        $invoices = Invoice::select('id', 'amount', 'amount_with_iva')
        ->where('order_id', $order->id)
        ->get();

        $refund = false;

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($order->customer_id); 

        //Cuenta del cliente:
        $customer_account = CompanyAccounts::where('company_id', $customer->id)
        ->where('status', 1)
        ->first();

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        //Cuenta del proveedor:
        $provider_account = CompanyAccounts::where('company_id', $provider->id)
        ->where('status', 1)
        ->first();

        //Configuraciones de proveedor:
        $provider_settings = CustomerProviders::select('accounting_concept_id', 'pay_after_days', 'payday', 'payment_method_id')
        ->where('customer_id', $customer->id)
        ->where('provider_id', $provider->id)
        ->first();

        //Cuentas de gastos del cliente a partir de su estructura de gastos:
        $structure_concepts = [];
        if($provider_settings->accounting_concept_id || $invoice->accounting_structure_id){
            //Si la factura ya tiene una estructura definida utilizamos ésta, si no la que tenga declarada el proveedor:
            $by = $invoice->accounting_structure_id? $invoice->accounting_structure_id:$provider_settings->accounting_concept_id;

            $structure_concepts = AccountingConcept::where('main', $by) 
            ->pluck('name', 'id')
            ->toArray();   

            //Si no hay concepto seleccionado:
            if(!$invoice->accounting_concept_id){
                $structure_concepts[''] = trans('textos.concepto_selec');
                ksort($structure_concepts);
            }
        }

        //Líneas del pedido: 
        $items = Item::where('order_id', $order->id)
        ->orderBy('concept', 'ASC')
        ->get();

        //Productos. Activos y aptos para venta:
        $products = Product::companyProducts($order->provider_id, $order->customer_id, true, true);

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $languages = $this->languages;

        $patterns = false;

        //Pagos:
        $payments = Payment::paymentsByInvoice($invoice->id);

        //Templates:
        $templates = [];

        //Template por defecto:
        $featuredTemplate = false;

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

            $order_amount = $arr_amounts['total_amount'];
            $order_amount_without_discount = $arr_amounts['total_amount_without_discounts'];
        } 

        //Métodos de pago:
        //$payment_methods = helpers::paymentMethods();
        $payment_methods = PaymentMethod::where('company_id', $customer->id)
        ->pluck('name', 'id')
        ->toArray();

        //Conceptos contables:
        $concepts = AccountingConcept::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->where('main', '0')
        ->pluck('name', 'id')
        ->toArray();

        //Cuentas bancarias:
        $bank_accounts = [];
        $bank_featured = false;
        $bank_accounts_data = BankAccounts::getBankAccounts($customer->id);
        foreach($bank_accounts_data as $ba){
            $bank = $ba->tradename? $ba->tradename:$ba->name;
            $bank_accounts[$ba->id] = $bank.' - '.$ba->account;

            if($ba->featured == 1) $bank_featured = $ba->id;
        }

        //Cuentas bancarias proveedor:
        $provider_bank_accounts = [];
        $provider_bank_featured = false;
        $provider_bank_accounts_data = BankAccounts::getBankAccounts($provider->id);
        foreach($provider_bank_accounts_data as $pba){
            $bank = $pba->tradename? $pba->tradename:$pba->name;
            $provider_bank_accounts[$pba->id] = $bank.' - '.$pba->account;

            if($pba->featured == 1) $provider_bank_featured = $pba->id;
        }


        return view('admin.invoices.edit')->with(compact('header', 'invoice', 'tab', 'is_sale', 'order', 'invoiced', 'invoices', 'refund', 'customer', 'customer_account', 'provider', 'provider_account', 'provider_settings', 'structure_concepts', 'items', 'products', 'currency', 'languages', 'patterns', 'payments', 'templates', 'featuredTemplate', 'permitted', 'module_budgets', 'module_products', 'module_projects', 'order_amount', 'payment_methods', 'concepts', 'bank_accounts', 'bank_featured', 'provider_bank_accounts', 'provider_bank_featured'));
    }

    /**
     * 3.2. Mostrar factura.
     */
    public function show(Invoice $invoice){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.factura_ver',
            'module' => $this->module,
            'slug' => 'invoices'
        ];  

        //Recalculando importe factura por sus líneas:
        //$this->recalculateInvoiceAmount($invoice);

        //Pedido:
        $order = Order::find($invoice->order_id); 

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($order->customer_id); 

        $cw_id = $order->customer_workplace_id;
        if($cw_id){
            $customer_workplace = Workplace::getWorkplace($cw_id);
        }else{
            $customer_workplace = Workplace::companyFeaturedWorkplace($order->customer_id);
        }

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        $pw_id = $order->provider_workplace_id;
        if($pw_id){
            $provider_workplace = Workplace::getWorkplace($pw_id);
        }else{
            $provider_workplace = Workplace::companyFeaturedWorkplace($order->provider_id);
        }

        $items = Item::where('order_id', $order->id)
        ->get();

        $languages = $this->languages;

        //Moneda:
        $currency = CompanySettings::co_currency(0);
    
        return view('admin.invoices.show')->with(compact('header', 'invoice', 'order', 'customer', 'customer_workplace', 'provider', 'provider_workplace', 'items', 'languages', 'currency'));  
    }

    /**
     * 3.3. Recalcular importe factura a partir de sus líneas.
     *
     * $invoice obj
     */
    public function recalculateInvoiceAmount($invoice, $order){
        //Recalculando importe factura por sus líneas:
        if($invoice->blocked == '0' && $invoice->order_id){
            //Importe total del pedido:
            $order_amount = $order->getAmount(true, true);
            $base_amount = $order_amount['total_amount'];
            $invoice_amount = $order_amount['invoice_amount'];
            //$arr_amount = InvoiceItems::setInvoiceAmount($invoice);
            
            //Descontando IRPF:
            if($invoice->irpf > 0){
                $desc_irpf = ($invoice->irpf * $base_amount) / 100;
                $invoice_amount = $invoice_amount - $desc_irpf;
            }

            $invoice->amount = $base_amount;
            $invoice->amount_with_iva = $invoice_amount;
            $invoice->save();
        }    
    }

    /**
     * 4. Actualizar factura.
     */
    public function update(Request $request, Invoice $invoice){
        //Discriminamos si el update lo hace el vendedor o el comprador:
        $order = $invoice->order_id? Order::select('id', 'customer_id', 'provider_id')->find($invoice->order_id):false;

        $is_sale = $order && $order->provider_id == session('currentCompany')? true:false;

        //Formateando fechas - condiciones:
        // 1. para ventas o compras a proveedores sin cuenta.
        // 2. facturas no bloqueadas.
        //Cuenta del proveedor:
        $provider_account = CompanyAccounts::where('company_id', $order->provider_id)
        ->where('status', 1)
        ->first();

        if( ($is_sale || !$provider_account) && !$invoice->blocked){
            $fiscal_date = $request->fiscal_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->fiscal_date):$request->fiscal_date:NULL;
            $expiration_date = $request->expiration_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->expiration_date):$request->expiration_date:NULL;

            $invoice->fiscal_date = $fiscal_date;
            $invoice->expiration_date = $expiration_date;
        }

        //Venta:
        if($is_sale){
            //Número de factura:
            $invoice_num = false;
            if($request->pattern_id){
                $patternNum = $this->setPatternNumber('invoice', $request->pattern_id);
            } 

            //Comprobamos si la fecha fiscal es válida, que el mes y año no estén cerrados.
            //29/12/22: preguntar a Marcelo si aplica también para la edición.
            // if($fiscal_date){
            //     $no_valid = AccountingOption::validInvoiceDate($fiscal_date, 's', session('currentCompany'));  
            //     if($no_valid){
            //         $alert = trans('textos.cierre_mensual_ventas_aviso');
            //         return redirect()->route('invoices.edit', $invoice->id)->with(compact('alert'));     
            //     }
            // }

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
                $invoice->status = $request->status? $request->status:'0';
                $invoice->bank_account_id = $request->bank_account_id;
            }
            $invoice->provider_observations = $request->observations;

        //Compra:
        }else{
            $check_in_date = $request->check_in_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->check_in_date):$request->check_in_date:NULL;

            //Subir documento.
            $doc = $this->loadFile($request, 'invoices');

            $invoice->customer_observations = $request->observations;  
            $invoice->accounting_structure_id = $request->accounting_structure;
            $invoice->accounting_concept_id = $request->accounting_concept;
            $invoice->payment_method_id = $request->payment_method;
            $invoice->irpf = $request->irpf;
            $invoice->check_in_date = $check_in_date;
            if($doc){
                $invoice->doc = $doc;
            }
        }

        //Recalculando descuento por IRPF:
        //if($order){
            //$order_amount = $order->getAmount();
            //$invoice->amount = $order_amount['total_amount'];

            if($request->irpf > 0){
                $desc_irpf = ($request->irpf * $invoice->amount) / 100;
                $invoice->amount_with_iva = $invoice->amount_with_iva - $desc_irpf;
            //}else{
                //$invoice->amount_with_iva = $order_amount['invoice_amount'];
            }
        //}

        $invoice->save();

        //Actualizando valores por defecto en la relación cliente-proveedor:
        $provider_settings = CustomerProviders::select('id', 'accounting_concept_id', 'expense_account_id', 'pay_after_days', 'payday', 'payment_method_id')
        ->where('customer_id', $order->customer_id)
        ->where('provider_id', $order->provider_id)
        ->first();

        if(!$provider_settings->pay_after_days){
            $provider_settings->pay_after_days = $request->pay_after_days;   
        }
        if(!$provider_settings->payday){
            $provider_settings->payday = $request->payday;    
        }
        if(!$provider_settings->accounting_concept_id){
            $provider_settings->accounting_concept_id = $request->accounting_structure;    
        }
        if(!$provider_settings->expense_account_id){
            $provider_settings->expense_account_id = $request->accounting_concept;    
        }
        if(!$provider_settings->payment_method_id){
            $provider_settings->payment_method_id = $request->payment_method;    
        }
        $provider_settings->save();

        $msg = trans('textos.factura_updated');
        $route = $is_sale? 'edit':'edit-purchase';
        return redirect()->route('invoices.'.$route, [$invoice, false])->with(compact('msg'));
    }

    /**
     * 5. Imprimir factura.
     */
    public function print(Invoice $invoice, $bn = false){
        if(!$invoice || $invoice->deleted_at){
            $alert = trans('textos.factura_no_existe');
            return redirect()->route('invoices.index')->with(compact('alert')); 
            exit;       
        }  

        //Recalculando importe factura por sus líneas:
        //$this->recalculateInvoiceAmount($invoice);

        $invoice_order = Order::select('orders.*', 'users.name AS Nombre', 'users.surname', 'projects.name AS project')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->leftJoin('project_orders', 'orders.id', '=', 'project_orders.order_id')
        ->leftJoin('projects', 'project_orders.project_id', '=', 'orders.id')
        ->where('orders.id', $invoice->order_id)
        ->first();

        $items = Item::where('order_id', $invoice_order->id)
        ->orderBy('concept', 'ASC')
        ->get();

        //Anticipos:
        //16/10/2022: revisar el campo order_id de las facturas migradas, pues es posible que no esté coincidiendo correctamente.
        $advance_invoices = Invoice::select('id', 'invoice_num', 'amount', 'with_iva', 'amount_with_iva')
        ->where('order_id', $invoice_order->id)
        ->get();

        //Proveedor:
        $provider = Company::find($invoice_order->provider_id);

        //Customizando CIF, ponemos espacio en blanco:
        $provider->nif = helpers::setCif($provider->nif);

        //Cliente:
        $customer = Company::find($invoice_order->customer_id);

        $template = $invoice->template? unserialize($invoice->template):false;

        $fechaLiteral = $invoice->fiscal_date? helpers::fechaLiteral($invoice->fiscal_date, session()->get('locale')):helpers::fechaLiteral($invoice->created_at, session()->get('locale'));

        $company_settings = session('companySettings');

        //Sede proveedor:
        $provider_workplace = false;
        $provider_phones = false;
        if($invoice_order->provider_workplace_id){
            $provider_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->join('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $invoice_order->provider_workplace_id)
            ->first();  

            $provider_phones = Phone::select('phone_number')
            ->where('workplace_id', $provider_workplace->id)
            ->get();
        }

        //Sede cliente:
        $customer_workplace = false;
        if($invoice_order->customer_workplace_id){
            $customer_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->join('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $invoice_order->customer_workplace_id)
            ->first();  
        }

        //Responsable cliente:
        $customer_manager = false;
        // if($invoice_order->customer_team){
        //     $responsibles = unserialize($invoice_order->customer_team);
            
        //     foreach($responsibles as $rsp){
        //         if(substr($rsp, -1) == '|'){
        //             $x = trim($rsp, '|');
        //             $customer_manager = User::select('name', 'surname')->find($x);  
        //             break;
        //         }
        //     }
        // }
        if($invoice_order->customer_manager_id){
            $customer_manager = User::select('name', 'surname')->find($invoice_order->customer_manager_id);   
        }

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Datos bancarios:
        $bank_account = false;
        if($invoice->bank_account_id){
            $bank_account = BankAccounts::select('banks.tradename', 'banks.swift', 'bank_accounts.account')
            ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
            ->find($invoice->bank_account_id);
        }

        //Actualizamos número de impresiones:
        $invoice->printed = $invoice->printed + 1;
        $invoice->save();

        //Nombre del archivo:
        $invoice_name_pdf = $invoice->invoice_num? $invoice->invoice_num:trans('textos.factura').'_'.$invoice->id;

        //Template de factura. Se pasa por variable por si se incluyen más templates.
        $invoice_template = 'pdf/invoice';

        $pdf = PDF::loadView($invoice_template, compact('invoice', 'bn', 'invoice_order', 'items', 'advance_invoices', 'provider', 'customer', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'provider_phones', 'customer_workplace', 'customer_manager', 'currency', 'bank_account'));
        return $pdf->download($invoice_name_pdf.'.pdf'); 
    }

    /**
     * 6. Setear info para listado de facturas.
     */
    public function setInvoicesData($data, $environment){
        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $arrEstados = helpers::invoiceStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        return DataTables::of($data) 
        ->addColumn('invoice_num', function($row){
            $num = $row->invoice_num? $row->invoice_num:($row->invoice_old_num? $row->invoice_old_num:$row->id);
            return '<a href="'.route('invoices.edit', $row).'">'.$num.'</a>'; 
        })
        ->addColumn('company', function($row) use($environment){
            return $row->name;
        })





        ->rawColumns(['invoice_num'])
        ->make(true);




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
                $c = CompanySettings::co_currency(0);
                $row->currency = $c->symbol;
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
     * 8.1. Desbloquear factura.
     */
    public function unlock(Invoice $invoice, $return = false){
        $invoice->blocked = false; 
        $invoice->save(); 

        $msg = trans('textos.factura_desbloqueada_ok');
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
        //if(!$invoice->template_id || $invoice->template_id != $request->template_id){
        if($request->template_id){
            if($invoice->template_id != $request->template_id){
                $new_template = InvoiceSettings::find($request->template_id);

                $template = [
                    'conditions' => $new_template->conditions, 
                    'payment_method' => $new_template->payment_method,
                    'observations' => $new_template->observations
                ];

            }else{
                //Se mantiene el mismo template_id:
                if($invoice->template_id == $request->template_id){
                    $template = [
                        'conditions' => $request->conditions, 
                        'payment_method' => $request->payment_method,
                        'observations' => $request->observations
                    ];

                }else{
                    $orig_template = InvoiceSettings::find($request->template_id);

                    $template = [
                        'conditions' => $orig_template->conditions, 
                        'payment_method' => $orig_template->payment_method,
                        'observations' => $orig_template->observations
                    ];
                }
            }
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

        $customer = Company::select('id', 'name')->where('id', $order->customer_id)->first();
        $provider = Company::select('id', 'name')->where('id', $order->provider_id)->first();

        //Importe del pedido:
        if($order->bag_amount){
            $order_amount = $order->bag_amount;
            $order_amount_without_discount = false;
        }else{
            $items = $order->items();
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['total_amount'];
            $order_amount_without_discount = $arr_amounts['total_amount_without_discounts'];
        } 

        //Facturas:
        $invoices = Invoice::where('order_id', $order->id)
        ->orderBy('fiscal_date', 'ASC')
        ->get();    

        //Estados de factura:
        $invoice_status = helpers::invoiceStatus();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

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
        $currency = CompanySettings::co_currency(0);

        $languages = $this->languages;

        return view('admin.invoices.invoices-by-order-group')->with(compact('header', 'order', 'customer', 'provider', 'invoices', 'group_amount', 'invoice_status', 'currency', 'languages'));
    }

    /**
     * 11. Cancelar factura.
     */
    public function destroy(Request $request, Invoice $invoice){
        $order_id = $invoice->order_id;

        //Comprobamos que reuna las condiciones para ser eliminada.
        /**
         * 1. La factura no debe estar bloqueda.
         */
        if($invoice->blocked == 1){
            $alert = trans('textos.accion_no_permitida');
            return redirect()->route('invoices.edit', $invoice)->with(compact('alert'));    
        }

        //Dejamos los pedidos como NO facturados:
        Order::where('id', $order_id)
        ->update(['orders.invoiced' => false]);

        //Eliminamos las previsiones de facturación asociadas a la factura:
        OrderPrevisions::where('invoice_id', $invoice->id)->delete();

        //Eliminamos la numeración customizada:
        $invoice->invoice_num = NULL;
        $invoice->num_cardinal = NULL;
        $invoice->pattern_id = NULL;
        $invoice->save();

        $invoice->delete();

        //Si no existen más facturas del mismo pedido, se dejan las líneas del pedido como no facturadas:
        $other_invoices = Invoice::select('id')->where('order_id', $order_id)->get();
        if(!$other_invoices->count()){
            //Marcamos las líneas del pedido como facturadas:
            Item::itemsAsInvoiced($order_id, false);    
        }

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
            $iva = $status == 1? CompanySettings::co_iva():NULL;

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
        //El importe indicado no puede superar al de la factura, teniendo en cuenta además otros abonos anteriores.
        $refunds = Invoice::where('refund', $invoice->id)
        ->sum('amount');
        $pending_amount = $invoice->amount - $refunds;
        
        //Abono parcial:
        if($request->refund == 'p' && $request->refund_amount > $pending_amount){
            $alert = trans('textos.factura_importe_superior_warning');
            return redirect()->route('invoices.edit', $invoice)->with(compact('alert')); 
            exit;     
        }
        
        //Importe de la factura de abono:
        if($request->refund == 'p'){    
            $refund_amount = $request->refund_amount;

            $iva_amount = helpers::percent($refund_amount, CompanySettings::co_iva());
            $refund_amount_with_iva = $refund_amount + $iva_amount;
  
            $refund_discount_amount = NULL;
        }else{
            $refund_amount = $pending_amount;

            $iva_amount = helpers::percent($refund_amount, CompanySettings::co_iva());

            $refund_amount_with_iva = $refund_amount + $iva_amount;
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
        $refund->provider_observations = $request->concept;
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

        //Actualizando previsiones de facturación: la desvinculamos de la factura y colocamos el porcentaje correcto.
        if($request->refund == 'p'){
            $order_previsions = OrderPrevisions::where('invoice_id', $invoice->order_id)
            ->get();   

            //Obtenemos el porcentaje que se mantiene facturado:
            $still_invoiced = $invoice->amount - $refund_amount;
            $still_invoiced_percent = ($still_invoiced * 100) / $invoice->amount;

            foreach($order_previsions as $op){
                //Actualizamos la factura inicial:
                if($op->invoice_id == $invoice->id){         
                    $op->percent = round($still_invoiced_percent, 2);
                    $op->save();
                }
            }

            //Creamos nueva previsión por el porcentaje pendiente:
            $new_order_prevision = new OrderPrevisions();
            $new_order_prevision->order_id = $invoice->order_id;
            $new_order_prevision->year = date('Y');
            $new_order_prevision->percent = round(($refund_amount * 100) / $invoice->amount, 2);
            $new_order_prevision->save();

        }elseif($request->refund == 't'){
            OrderPrevisions::where('invoice_id', $invoice->id)
            ->update(['month' => NULL, 'percent' => 100, 'invoice_id' => NULL]);
        }

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
            $invoice_amount = $invoice_amount + $amount['total_amount'];
        }

        $invoice->amount = $invoice_amount;

        //Cálculo IVA:
        if($invoice_amount > 0 && $invoice->with_iva){
            $iva_amount = helpers::percent($invoice_amount, CompanySettings::co_iva());
            $amount_with_iva = $invoice_amount + $iva_amount;
        }
        $invoice->amount_with_iva = $amount_with_iva;
        $invoice->save();      

        $msg = trans('textos.factura_agrupacion_pedido_eliminado');
        return redirect()->route('invoices.edit', $invoice)->with(compact('msg'));
    }

    /**
     * 15. Opciones contables.
     */
    private function accountingOptions(){
        return AccountingOption::getCompanyOptions();
    }

    /**
     * 16. Control de facturación.
     */
    public function control(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.facturacion_control',
            'module' => $this->module,
            'slug' => 'invoices'
        ];      

        return view('admin.invoices.control-list')->with(compact('header'));
    }

    /**
     * 16.1. Control de facturación para DataTable.
     */
    public function getControlDT(){
        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $estados = helpers::orderStatus();

        $data = Order::select('orders.id', 'orders.order_num', 'orders.order_old_num', 'orders.delivery_date', 'orders.order_status', 'companies.name', 'companies.tradename', 'users.name AS Nombre', 'users.surname')
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->whereHas('previsions', function ($query){
            $query->whereNull('invoice_id');
            $query->select(DB::raw('SUM(percent) as total'))
            ->havingRaw('total < 100');
        })
        ->where('orders.provider_id', session('currentCompany'))
        ->with('previsions')
        ->orderBy('orders.id', 'ASC');

        return DataTables::of($data)
        ->addColumn('order_num', function($row){
            $num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
            return '<a href="'.route('orders.edit', $row).'">'.$num.'</a>'; 
        })
        ->addColumn('company', function($row){
            return $row->name;
        })
        ->addColumn('manager', function($row){
            return $row->Nombre.' '.$row->surname;
        })
        ->addColumn('delivery_date', function($row){
            return $row->delivery_date? Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]):'';
        })
        ->addColumn('amount', function($row) use($currency){
            $amount = $row->getAmount();  
            return $amount['total_amount'].$currency->symbol;
        })
        ->addColumn('pendiente', function($row) use($currency){
            $amount = $row->getAmount();
            $invoice_amounts = $row->getInvoicedAmount();
            $diff = isset($invoice_amounts['invoiced_amount'])? $amount['total_amount'] - $invoice_amounts['invoiced_amount']:false;

            return $diff? round($diff, 2).$currency->symbol:'';
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.orders.partials.dt-status', compact('row', 'estados'));    
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.invoices.partials.dt-actions-control', compact('row', 'estados'));    
        })
        ->rawColumns(['order_num'])
        ->make(true);
    }

    /**
     * 17. Eliminar documento.
     */
    public function removeDoc(Invoice $invoice){
        File::delete(public_path().'/docs/invoices/'.$invoice->doc);

        $invoice->doc = NULL;
        $invoice->save();

        $msg = trans('textos.archivo_deleted');
        return redirect()->route('invoices.edit-purchase', $invoice)->with(compact('msg'));
    }
    
}

