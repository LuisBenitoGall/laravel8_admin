<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;
use PDF;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderPrevisions;
use App\Models\Phone;
use App\Models\User;
use App\Models\Workplace;

class OrderGroupsController extends Controller{
    /**
     * 1. Listado de agrupaciones de pedidos.   
     * 1.1. Lista de agrupaciones por cliente.   
     * 1.2. Listado de agrupaciones por cliente para DataTable.   
     * 2. Formulario nueva agrupación.
     * 3. Agregar pedidos a la agrupación. 
     * 3.1. Vincular pedido a agrupación.
     * 4. Eliminar pedido de agrupación.   
     * 5. Selección de pedidos para facturar.
     * 6. Imprimir agrupación.
     */ 
    
    private $option = 'pedidos';
    private $module = 'orders';
    private $languages;  
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:orders');
        $this->middleware('order_groups_get', ['only' => ['edit']]);
        $this->middleware('order_post', ['only' => ['addOrders', 'addOrder_']]);
        $this->middleware('order_get', ['only' => ['destroy', 'toInvoice']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación index:
        if(isset($data['submit_filter'])){
            return Validator::make($data, [
                'customer_id' => 'required_without:og_id|integer'
            ],[
                'customer_id.required_without' => trans('textos.required_cliente_agrupacion')
            ]);

        //Validación guardar:
        }elseif(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'customer_id' => 'required|integer',
                'bag_amount' => 'nullable|numeric'
            ]);

        //Validación actualizar:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'status' => 'nullable|integer',
                'pay_order' => 'nullable|max:100'
            ]);

        //Validación añadir pedidos:
        }elseif(isset($data['submit_add_orders'])){
            return Validator::make($data, [
                'order_id' => 'required|integer',
                'orders' => 'required'
            ],[
                'orders.required' => trans('textos.campo_obligatorio')
            ]);

        //Validación añadir pedido:
        }elseif(isset($data['submit_add_order'])){
            return Validator::make($data, [
                'order_id' => 'required|integer',
                'order_group' => 'required'
            ],[
                'order_group.required' => trans('textos.campo_obligatorio')
            ]);
        }
    }

    /**
     * 1. Listado de agrupaciones de pedidos.
     */
    public function index($customer_id = false, $year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agrupaciones',
            'module' => $this->module,
            'slug' => 'order-groups'
        ]; 

        $year = $year? $year:date('Y');
        $customer = $customer_id? Company::find($customer_id):false;
        $provider = Company::find(session('currentCompany'));

        //Comprobamos si la empresa hace gestión de clientes:
        if(session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }else{
            $customers = false;
        }

        return view('admin.order_groups.index')->with(compact('header', 'year', 'customer', 'provider', 'customers'));
    }

    /**
     * 1.1. Lista de agrupaciones por cliente.
     */
    public function getGroupsBy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/order-groups/list')
                ->withErrors($validator)
                ->withInput();
        }

        //Si viene declarado el número de agrupación redirigimos:
        if($request->og_id){
            //Comprobamos si el valor recibido corresponde a id:
            $og = Order::select('id')->find($request->og_id);

            if(!$og){
                $alert = trans('textos.agrupacion_no_existe');
                return redirect()->route('order-groups.index')->with(compact('alert'));   
                exit;
            }else{
                return redirect()->route('orders.edit', $og->id);    
                exit;   
            }
        }

        return redirect()->route('order-groups.index', [$request->customer_id, $request->year]);
    }

    /**
     * 1.2. Listado de agrupaciones por cliente para DataTable.
     */
    public function getGroupsDT($customer = false, $year = false){
        if(!$customer || !$year){
            return false;
            exit;
        }

        $data = Order::select('orders.id', 'orders.order_num', 'orders.order_old_num', 'orders.name', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.created_at', 'orders.bag_amount', 'orders.order_status')
        ->where('orders.customer_id', $customer)
        ->where('orders.provider_id', session('currentCompany'))
        ->where('orders.is_group', 1)
        ->whereYear('orders.created_at', '=', $year);

        $estados = helpers::orderGroupStatus();

        //Permisos del usuario:
        $editar = Auth::user()->can('orders.edit');
        $eliminar = Auth::user()->can('orders.destroy');

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('num', function($row){
            return view('admin.orders.partials.dt-num', compact('row')); 
        })
        ->addColumn('purchase', function($row){
            $purchase = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:'');
            return $purchase;
        })
        ->filterColumn('purchase', function($query, $keyword){
            $sql = "orders.purchase_num = ? OR orders.purchase_old_num = ?";
            $query->whereRaw($sql, $keyword);
        })
        ->addColumn('alta', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);    
        })
        ->addColumn('group_amount', function($row) use($currency){
            $amount = $row->getGroupAmount();
            return $amount.$currency->symbol; 
        })     
        ->addColumn('bag_amount', function($row) use($currency){
            return $row->bag_amount.$currency->symbol; 
        }) 
        ->addColumn('estado', function($row) use($estados){
            return view('admin.orders.partials.dt-status', compact('row', 'estados'));    
        })
        ->addColumn('acciones', function($row) use($estados){
            $manage_status = false;
            return view('admin.orders.partials.dt-actions', compact('row', 'estados', 'manage_status'));    
        })
        ->make(true);

//REVISAR LAS CONDICIONES SIGUIENTES

        //Ampliación de información de los objetos:
        // if($data){
        //     //Comprobamos si tiene activo el módulo de contabilidad para poder facturar el pedido:
        //     $invoice = $this->settedModule('contabilidad');

        //     foreach($groups as $row){


        //         //Agrupación abierta:
        //         $row->opened = in_array($row->status, config('constants.ORDER_GROUPS_OPENED_'))? true:false;


        //         //Facturación de pedido. El pedido debe estar en algún status que permita su facturación:
        //         $row->invoice = in_array($row->status, config('constants.ORDER_STATUS_INVOICED_'))? $invoice:false;
        //     }  
        // }

        //return datatables($groups)->toJson();
    }

    /**
     * 2. Formulario nueva agrupación.
     */
    public function create($customer_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agrupacion_nueva',
            'module' => $this->module,
            'slug' => 'order-groups'
        ]; 

        //Comprobamos si la empresa hace gestión de clientes:
        if(session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }else{
            $customers = false;
        }   

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return view('admin.order_groups.create')->with(compact('header', 'customers', 'customer_id', 'currency')); 
    }

    /**
     * 3. Agregar pedidos a la agrupación.
     */
    public function addOrders(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-orders');

            return redirect('/admin/orders/'.$request->order_id.'/edit/orders')
                ->withErrors($validator)
                ->withInput();
        }    

        foreach($request->orders as $o){
            //Pasamos como condición que el pedido no esté facturado.
            Order::where('id', $o)
            ->where('invoiced', false)
            ->update(['order_parent' => $request->order_id, 'is_group' => false]);
        }

        $msg = trans('textos.pedidos_en_agrupacion');
        return redirect()->route('orders.edit', [$request->order_id, 'orders'])->with(compact('msg'));
    }

    /**
     * 3.1. Vincular pedido a agrupación.
     */
    public function addOrder_(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-link-order-group');

            return redirect('/admin/orders/'.$request->order_id.'/edit/orders')
                ->withErrors($validator)
                ->withInput();
        }  

        Order::where('id', $request->order_id)
        ->where('invoiced', false)
        ->update(['order_parent' => $request->order_group, 'is_group' => false]);

        $msg = trans('textos.pedidos_en_agrupacion');
        return redirect()->route('orders.edit', $request->order_id)->with(compact('msg'));    
    }

    /**
     * 4. Eliminar pedido de agrupación.
     */
    public function destroy(Request $request, Order $order){
        $order_parent = $order->order_parent;

        $order->order_parent = NULL;
        $order->bag_amount = 0;
        $order->save();

        $msg = trans('textos.pedido_deleted_grupo');
        if($request->return == 'order-single'){
            return redirect()->route('orders.edit', $order->id)->with(compact('msg'));
        }elseif($request->return == 'order-group'){
            return redirect()->route('orders.edit', [$order_parent, 'orders'])->with(compact('msg'));
        }
    }

    /**
     * 5. Selección de pedidos para facturar.
     */
    public function toInvoice(Order $order){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agrupacion_facturar',
            'module' => $this->module,
            'slug' => 'order-groups'
        ];          

        $ordersInGroup = $order->getGroupOrders();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return view('admin.order_groups.invoices')->with(compact('header', 'order', 'ordersInGroup', 'currency'));
    }

    /**
     * 6. Imprimir agrupación.
     */
    public function print(Order $order){
        if(!$order){
            return redirect('/');
            exit;   
        }

        //Importe de la agrupación:
        $orderGroupAmount = false;
        if($order->is_group){
            $orderGroupAmount = $order->getGroupAmount();    
        }

        $provider = Company::find($order->provider_id);
        $customer = Company::find($order->customer_id);

        $template = $order->order_template? unserialize($order->order_template):false;

        $owner = User::select('name', 'surname')->find($order->provider_manager_id);

        $fechaLiteral = helpers::fechaLiteral($order->created_at, session()->get('locale'));

        $company_settings = session('companySettings');

        //Sede proveedor:
        if($order->provider_workplace_id){
            $provider_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->join('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $order->provider_workplace_id)
            ->first();  

            $provider_phones = Phone::select('phone_number')
            ->where('workplace_id', $provider_workplace->id)
            ->get();
        }else{
            $provider_workplace = false;
            $provider_phones = false;
        }

        //Sede cliente:
        if($order->workplace_id){
            $customer_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->join('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $order->workplace_id)
            ->first();  

        }else{
            $customer_workplace = false;
        }

        //Provider manager:
        $provider_manager = false;
        if($order->provider_manager_id){
            $provider_manager = User::select('name', 'surname')->find($order->provider_manager_id);
        }   

        //Customer manager:
        $customer_manager = false;
        if($order->customer_manager_id){
            $customer_manager = User::select('name', 'surname')->find($order->customer_manager_id);
        }    

        //Moneda:
        $currency = $this->co_currency;

        //Título .pdf:
        $pdf_title = $order->order_num? helpers::slugFiles($order->order_num, '_', false):$order->id;

        $pdf = PDF::loadView('pdf/order-group', compact('order', 'orderGroupAmount', 'provider', 'customer', 'owner', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'provider_phones', 'customer_workplace', 'provider_manager', 'customer_manager', 'currency'));
        return $pdf->download($pdf_title.'.pdf'); 
    }
}
