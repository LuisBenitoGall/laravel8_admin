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
use Carbon\Carbon;

//Models:
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserCompanies;

class OrderQueryController extends Controller{
    /**
     * 1. Listado de consultas.
     * 2. Por pedidos.
     * 2.1. Consulta de pedidos para DataTables.
     * 3. Por líneas de pedido.
     * 3.1. Consulta items de pedidos para DataTables.
     * 4. Por presupuestos.
     * 4.1. Consulta de presupuestos para DataTables.
     * 5. Por líneas de presupuesto.
     * 5.1. Consulta items de presupuestos para DataTables.
     * 6. Setear centro de coste de proveedor para líneas NULL.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:orders');
    }

    /**
     * 1. Listado de consultas.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.consultas',
            'module' => $this->module,
            'slug' => 'orders'
        ];  

        //Relación de consultas:
        $queries = [
            ['por_pedidos', 'by-orders'],
            ['por_lineas_pedido', 'by-order-items'],
            ['por_presupuestos', 'by-budgets'],
            ['por_lineas_presupuesto', 'by-budget-items']
        ];

        return view('admin.order_queries.index')->with(compact('header', 'queries'));
    }

    /**
     * 2. Por pedidos.
     */
    public function byOrders(Request $request){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.por_pedidos',
            'module' => $this->module,
            'slug' => 'orders'
        ];  

        if(count($request->all())){
            $with_query = true;
            $companies_ = $request->companies;
            $from_ = $request->from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:date('Y-m-d');
            $to_ = $request->to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:date('Y-m-d');
            $status_ = $request->status? $request->status:'all';

            //Ventas o compras:
            $order_type = $request->order_type;

        }else{
            $with_query = false;
            $companies_ = [];
            $from_ = date('Y-m-d');
            $to_ = date('Y-m-d');
            $status_ = 'all';

            //Ventas o compras:
            $order_type = 'v';
        }

        //Empresas vinculadas al usuario:
        $user_companies = $this->getUserCompanies(Auth::user()->id);

        //Estados de pedido:
        $order_status = helpers::orderStatus();

        $languages = $this->languages;   

        return view('admin.order_queries.by-orders')->with(compact('header', 'with_query', 'companies_', 'from_', 'to_', 'status_', 'order_type', 'user_companies', 'order_status', 'languages')); 
    }

    /**
     * 2.1. Consulta de pedidos para DataTables.
     */
    public function getOrdersDT(Request $request, $companies, $order_type, $status, $from = false, $to = false){
        if($request->ajax()){
            $companies = $companies? explode(',', $companies):[];
            $from = $from? date("Y-m-d", $from):false;
            $to = $to? date("Y-m-d", $to):false;

            $order_status = helpers::orderStatus();

            $data = Order::select('orders.id', 'orders.is_order', 'orders.is_group', 'orders.is_budget', 'orders.invoiced', 'orders.order_num', 'orders.order_old_num', 'orders.budget_num', 'orders.budget_old_num', 'orders.name AS order_name', 'orders.accepted_date', 'orders.delivery_date', 'orders.bag_amount', 'orders.created_at', 'orders.order_status', 'cp.name AS provider', 'cc.name AS customer', 'cfc.name AS final_customer', 'up.name AS pm_name', 'up.surname AS pm_surname', 'ucom.name AS com_name', 'ucom.surname AS com_surname', 'uc.name AS cm_name', 'uc.surname AS cm_surname', 'ufc.name AS fcm_name', 'ufc.surname AS fcm.surname')
            ->join('companies AS cp', 'orders.provider_id', '=', 'cp.id')
            ->join('companies AS cc', 'orders.customer_id', '=', 'cc.id')
            ->leftJoin('companies AS cfc', 'orders.final_customer_id', '=', 'cfc.id')
            ->leftJoin('users AS up', 'orders.provider_manager_id', '=', 'up.id')
            ->leftJoin('users AS ucom', 'orders.commercial_id', '=', 'ucom.id')
            ->leftJoin('users AS uc', 'orders.customer_manager_id', '=', 'uc.id')
            ->leftJoin('users AS ufc', 'orders.final_customer_manager_id', '=', 'ufc.id')
            ->where('orders.is_order', true)
            ->whereNull('orders.deleted_at')
            // Intervalo fechas (por fecha de creación):
            ->when($from && $to, function($q) use($from, $to){
                return $q->whereBetween('orders.created_at', [$from, $to]);
            })
            //Ventas:
            ->when($order_type == 'v', function($q) use($companies){
                return $q->whereIn('orders.provider_id', $companies);
            })
            //Compras:
            ->when($order_type == 'c', function($q) use($companies){
                return $q->whereIn('orders.customer_id', $companies);
            })
            //Estado:
            ->when($status && $status != 'all', function($q) use($status){
                return $q->where('orders.order_status', $status);
            })
            ->orderBy('orders.id', 'ASC');

            //Tabla:
            return DataTables::of($data)
            ->addColumn('provider', function($row){
                return $row->provider; 
            })
            ->addColumn('customer', function($row){
                return $row->customer; 
            })
            ->addColumn('final_customer', function($row){
                return $row->final_customer; 
            })
            ->addColumn('is_order', function($row){
                if($row->is_order == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('is_budget', function($row){
                if($row->is_budget == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('is_group', function($row){
                if($row->is_group == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('budget', function($row){
                $budget = $row->budget_num? $row->budget_num:($row->budget_old_num? $row->budget_old_num:$row->id);
                return '<a target="_blank" href="/admin/budgets/'.$row->id.'/edit">'.$budget.'</a>'; 
            })
            ->addColumn('order', function($row){
                $order = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
                return '<a target="_blank" href="/admin/orders/'.$row->id.'/edit">'.$order.'</a>'; 
            })
            ->addColumn('amount', function($row){
                $amount = $row->getAmount();
                return $amount['total_amount'];
            })
            ->addColumn('order_name', function($row){
                return $row->order_name; 
            })
            ->addColumn('created_at', function($row){
                return $row->created_at->format($this->languages[session()->get('locale')][4]);
            })
            ->addColumn('accepted', function($row){
                return $row->accepted_date? Carbon::parse($row->accepted_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('status', function($row) use($order_status){
                if($row->order_status && isset($order_status[$row->order_status])){
                    return ucfirst($order_status[$row->order_status]);
                }else{
                    return '';
                }
            })
            ->addColumn('invoiced', function($row){
                if($row->invoiced == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('provider_manager', function($row){
                if($row->pm_name){
                    return $row->pm_name.' '.$row->pm_surname;
                }
            })
            ->addColumn('commercial', function($row){
                if($row->com_name){
                    return $row->com_name.' '.$row->com_surname;
                }
            })
            ->addColumn('customer_manager', function($row){
                if($row->cm_name){
                    return $row->cm_name.' '.$row->cm_surname;
                }
            })
            ->addColumn('final_customer_manager', function($row){
                if($row->fcm_name){
                    return $row->fcm_name.' '.$row->fcm_surname;
                }
            })
            ->addColumn('bag', function($row){
                if($row->bag_amount > 0){
                    $row->bag_amount;
                }
            })
            ->rawColumns(['is_order', 'is_budget', 'is_group', 'invoiced', 'budget', 'order'])
            ->make(true);
        }    
    }

    /**
     * 3. Por líneas de pedido.
     */
    public function byOrderItems(Request $request){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.por_lineas_pedido',
            'module' => $this->module,
            'slug' => 'orders'
        ];  

        if(count($request->all())){
            $with_query = true;
            $companies_ = $request->companies;
            $from_ = $request->from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:date('Y-m-d');
            $to_ = $request->to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:date('Y-m-d');
            $status_ = $request->status? $request->status:'all';

            //Ventas o compras:
            $order_type = $request->order_type;

        }else{
            $with_query = false;
            $companies_ = [];
            $from_ = date('Y-m-d');
            $to_ = date('Y-m-d');
            $status_ = 'all';

            //Ventas o compras:
            $order_type = 'v';
        }

        //Empresas vinculadas al usuario:
        $user_companies = $this->getUserCompanies(Auth::user()->id);

        //Estados de pedido:
        $order_status = helpers::orderStatus();

        $languages = $this->languages;   

        return view('admin.order_queries.by-order-items')->with(compact('header', 'with_query', 'companies_', 'from_', 'to_', 'order_type', 'status_', 'user_companies', 'order_status', 'languages'));    
    }

    /**
     * 3.1. Consulta items de pedidos para DataTables.
     */
    public function getOrderItemsDT(Request $request, $companies, $order_type, $status, $from = false, $to = false){
        if($request->ajax()){
            $companies = $companies? explode(',', $companies):[];
            $from = $from? date("Y-m-d", $from):false;
            $to = $to? date("Y-m-d", $to):false;

            $order_status = helpers::orderStatus();

            $data = Item::select('items.id', 'items.order_id', 'items.concept', 'items.qty', 'items.price', 'items.iva', 'items.irpf', 'items.discount', 'items.days', 'items.days_continue', 'items.days_continue_discount', 'p.name AS product', 'pp.name AS purchase_product', 'bfi.name AS budget_form_block', 'o.is_order', 'o.is_budget', 'o.invoiced', 'o.order_num', 'o.order_old_num', 'o.budget_num', 'o.budget_old_num', 'o.name AS order_name', 'o.accepted_date', 'o.delivery_date', 'o.bag_amount', 'o.created_at', 'o.order_status', 'cp.name AS provider', 'cc.name AS customer', 'cfc.name AS final_customer', 'up.name AS pm_name', 'up.surname AS pm_surname', 'ucom.name AS com_name', 'ucom.surname AS com_surname', 'uc.name AS cm_name', 'uc.surname AS cm_surname', 'ufc.name AS fcm_name', 'ufc.surname AS fcm.surname', 'pcc.name AS p_cost_center', 'ccc.name AS c_cost_center')
            ->join('orders AS o', 'items.order_id', '=', 'o.id')
            ->leftJoin('products AS p', 'items.product_id', '=', 'p.id')
            ->leftJoin('purchase_products AS pp', 'items.purchase_product_id', '=', 'pp.id')
            ->leftJoin('budget_form_items AS bfi', 'items.budget_form_block_id', '=', 'bfi.id')
            ->join('companies AS cp', 'o.provider_id', '=', 'cp.id')
            ->join('companies AS cc', 'o.customer_id', '=', 'cc.id')    
            ->leftJoin('companies AS cfc', 'o.final_customer_id', '=', 'cfc.id')
            ->leftJoin('users AS up', 'o.provider_manager_id', '=', 'up.id')
            ->leftJoin('users AS ucom', 'o.commercial_id', '=', 'ucom.id')
            ->leftJoin('users AS uc', 'o.customer_manager_id', '=', 'uc.id')
            ->leftJoin('users AS ufc', 'o.final_customer_manager_id', '=', 'ufc.id')
            ->leftJoin('cost_centers AS pcc', 'items.provider_cost_center_id', '=', 'pcc.id')
            ->leftJoin('cost_centers AS ccc', 'items.customer_cost_center_id', '=', 'ccc.id')

            ->where('o.is_order', true)
            ->whereNull('items.deleted_at')
            // Intervalo fechas (por fecha de creación):
            ->when($from && $to, function($q) use($from, $to){
                return $q->whereBetween('o.created_at', [$from, $to]);
            })
            //Ventas:
            ->when($order_type == 'v', function($q) use($companies){
                return $q->whereIn('o.provider_id', $companies);
            })
            //Compras:
            ->when($order_type == 'c', function($q) use($companies){
                return $q->whereIn('o.customer_id', $companies);
            })
            //Estado:
            ->when($status && $status != 'all', function($q) use($status){
                return $q->where('o.order_status', $status);
            })
            ->orderBy('o.id', 'ASC');

            //Tabla:
            return DataTables::of($data)
            ->addColumn('provider', function($row){
                return $row->provider; 
            })
            ->addColumn('customer', function($row){
                return $row->customer; 
            })
            ->addColumn('final_customer', function($row){
                return $row->final_customer; 
            })
            ->addColumn('is_order', function($row){
                if($row->is_order == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('is_budget', function($row){
                if($row->is_budget == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('budget', function($row){
                $budget = $row->budget_num? $row->budget_num:($row->budget_old_num? $row->budget_old_num:$row->order_id);
                return '<a target="_blank" href="/admin/budgets/'.$row->order_id.'/edit">'.$budget.'</a>'; 
            })
            ->addColumn('order', function($row){
                $order = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->order_id);
                return '<a target="_blank" href="/admin/orders/'.$row->order_id.'/edit">'.$order.'</a>'; 
            })
            ->addColumn('order_name', function($row){
                return $row->order_name; 
            })
            ->addColumn('created_at', function($row){
                return $row->created_at->format($this->languages[session()->get('locale')][4]);
            })
            ->addColumn('accepted', function($row){
                return $row->accepted_date? Carbon::parse($row->accepted_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('delivered', function($row){
                return $row->delivery_date? Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('status', function($row) use($order_status){
                if($row->order_status && isset($order_status[$row->order_status])){
                    return ucfirst($order_status[$row->order_status]);
                }else{
                    return '';
                }
            })
            ->addColumn('invoiced', function($row){
                if($row->invoiced == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('provider_manager', function($row){
                if($row->pm_name){
                    return $row->pm_name.' '.$row->pm_surname;
                }
            })
            ->addColumn('commercial', function($row){
                if($row->com_name){
                    return $row->com_name.' '.$row->com_surname;
                }
            })
            ->addColumn('customer_manager', function($row){
                if($row->cm_name){
                    return $row->cm_name.' '.$row->cm_surname;
                }
            })
            ->addColumn('final_customer_manager', function($row){
                if($row->fcm_name){
                    return $row->fcm_name.' '.$row->fcm_surname;
                }
            })
            ->addColumn('bag', function($row){
                if($row->bag_amount > 0){
                    $row->bag_amount;
                }
            })
            ->addColumn('product', function($row){
                return $row->product;
            })
            ->addColumn('concept', function($row){
                return $row->concept;
            })
            ->addColumn('budget_form_block', function($row){
                return $row->budget_form_block;
            })
            ->addColumn('accounting_product', function($row){
                return $row->purchase_product;
            })
            ->addColumn('qty', function($row){
                return $row->qty;
            })
            ->addColumn('price', function($row){
                return $row->price;
            })
            ->addColumn('iva', function($row){
                return $row->iva;
            })
            ->addColumn('irpf', function($row){
                return $row->irpf;
            })
            ->addColumn('discount', function($row){
                if($row->discount > 0){
                    return $row->discount.'%';
                }
            })
            ->addColumn('by_percent', function($row){

            })
            ->addColumn('days', function($row){
                return $row->days;
            })
            ->addColumn('days_continue', function($row){
                return $row->days_continue;
            })
            ->addColumn('days_continue_discount', function($row){
                if($row->days_continue_discount > 0){
                    return $row->days_continue_discount.'%';
                }
            })
            ->addColumn('amount', function($row){
                return round(Item::getItemAmount($row), 2);
            })
            ->addColumn('provider_cost_center', function($row){
                return $row->p_cost_center;
            })
            ->addColumn('customer_cost_center', function($row){
                return $row->c_cost_center;
            })
            ->rawColumns(['is_order', 'is_budget', 'invoiced', 'budget', 'order'])
            ->make(true);
        }
    }

    /**
     * 4. Por presupuestos.
     */
    public function byBudgets(Request $request){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.por_presupuestos',
            'module' => $this->module,
            'slug' => 'orders'
        ]; 

        if(count($request->all())){
            $with_query = true;
            $companies_ = $request->companies;
            $from_ = $request->from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:date('Y-m-d');
            $to_ = $request->to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:date('Y-m-d');
             $status_ = $request->status? $request->status:'all';

            //Ventas o compras:
            $order_type = $request->order_type;

        }else{
            $with_query = false;
            $companies_ = [];
            $from_ = date('Y-m-d');
            $to_ = date('Y-m-d');
            $status_ = 'all';

            //Ventas o compras:
            $order_type = 'v';
        }

        //Empresas vinculadas al usuario:
        $user_companies = $this->getUserCompanies(Auth::user()->id);

        //Estados de presupuesto:
        $budget_status = helpers::budgetStatus();

        $languages = $this->languages;   

        return view('admin.order_queries.by-budgets')->with(compact('header', 'with_query', 'companies_', 'from_', 'to_', 'status_', 'order_type', 'user_companies', 'budget_status', 'languages'));      
    }

    /**
     * 4.1. Consulta de presupuestos para DataTables.
     */
    public function getBudgetsDT(Request $request, $companies, $order_type, $status, $from = false, $to = false){
        if($request->ajax()){
            $companies = $companies? explode(',', $companies):[];
            $from = $from? date("Y-m-d", $from):false;
            $to = $to? date("Y-m-d", $to):false;

            $budget_status = helpers::budgetStatus();
            
            $data = Order::select('orders.id', 'orders.is_order', 'orders.is_group', 'orders.is_budget', 'orders.invoiced', 'orders.order_num', 'orders.order_old_num', 'orders.budget_num', 'orders.budget_old_num', 'orders.name AS order_name', 'orders.accepted_date', 'orders.delivery_date', 'orders.bag_amount', 'orders.created_at', 'orders.budget_status', 'cp.name AS provider', 'cc.name AS customer', 'cfc.name AS final_customer', 'up.name AS pm_name', 'up.surname AS pm_surname', 'ucom.name AS com_name', 'ucom.surname AS com_surname', 'uc.name AS cm_name', 'uc.surname AS cm_surname', 'ufc.name AS fcm_name', 'ufc.surname AS fcm.surname')
            ->join('companies AS cp', 'orders.provider_id', '=', 'cp.id')
            ->join('companies AS cc', 'orders.customer_id', '=', 'cc.id')
            ->leftJoin('companies AS cfc', 'orders.final_customer_id', '=', 'cfc.id')
            ->leftJoin('users AS up', 'orders.provider_manager_id', '=', 'up.id')
            ->leftJoin('users AS ucom', 'orders.commercial_id', '=', 'ucom.id')
            ->leftJoin('users AS uc', 'orders.customer_manager_id', '=', 'uc.id')
            ->leftJoin('users AS ufc', 'orders.final_customer_manager_id', '=', 'ufc.id')
            ->where('orders.is_budget', true)
            ->whereNull('orders.deleted_at')
            // Intervalo fechas (por fecha de creación):
            ->when($from && $to, function($q) use($from, $to){
                return $q->whereBetween('orders.created_at', [$from, $to]);
            })
            //Ventas:
            ->when($order_type == 'v', function($q) use($companies){
                return $q->whereIn('orders.provider_id', $companies);
            })
            //Compras:
            ->when($order_type == 'c', function($q) use($companies){
                return $q->whereIn('orders.customer_id', $companies);
            })
            //Estado:
            ->when($status && $status != 'all', function($q) use($status){
                return $q->where('orders.budget_status', $status);
            })
            ->orderBy('orders.id', 'ASC');

            //Tabla:
            return DataTables::of($data)
            ->addColumn('provider', function($row){
                return $row->provider; 
            })
            ->addColumn('customer', function($row){
                return $row->customer; 
            })
            ->addColumn('final_customer', function($row){
                return $row->final_customer; 
            })
            ->addColumn('is_order', function($row){
                if($row->is_order == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('is_budget', function($row){
                if($row->is_budget == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('is_group', function($row){
                if($row->is_group == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('budget', function($row){
                $budget = $row->budget_num? $row->budget_num:($row->budget_old_num? $row->budget_old_num:$row->id);
                return '<a target="_blank" href="/admin/budgets/'.$row->id.'/edit">'.$budget.'</a>'; 
            })
            ->addColumn('order', function($row){
                $order = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
                return '<a target="_blank" href="/admin/orders/'.$row->id.'/edit">'.$order.'</a>'; 
            })
            ->addColumn('amount', function($row){
                $amount = $row->getAmount();
                return $amount['total_amount'];
            })
            ->addColumn('order_name', function($row){
                return $row->order_name; 
            })
            ->addColumn('created_at', function($row){
                return $row->created_at->format($this->languages[session()->get('locale')][4]);
            })
            ->addColumn('accepted', function($row){
                return $row->accepted_date? Carbon::parse($row->accepted_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('status', function($row) use($budget_status){
                if($row->budget_status && isset($budget_status[$row->budget_status])){
                    return ucfirst($budget_status[$row->budget_status]);
                }else{
                    return '';
                }
            })
            ->addColumn('invoiced', function($row){
                if($row->invoiced == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('provider_manager', function($row){
                if($row->pm_name){
                    return $row->pm_name.' '.$row->pm_surname;
                }
            })
            ->addColumn('commercial', function($row){
                if($row->com_name){
                    return $row->com_name.' '.$row->com_surname;
                }
            })
            ->addColumn('customer_manager', function($row){
                if($row->cm_name){
                    return $row->cm_name.' '.$row->cm_surname;
                }
            })
            ->addColumn('final_customer_manager', function($row){
                if($row->fcm_name){
                    return $row->fcm_name.' '.$row->fcm_surname;
                }
            })
            ->addColumn('bag', function($row){
                if($row->bag_amount > 0){
                    $row->bag_amount;
                }
            })
            ->rawColumns(['is_order', 'is_budget', 'is_group', 'invoiced', 'budget', 'order'])
            ->make(true);
        }    
    }

    /**
     * 5. Por líneas de presupuesto.
     */
    public function byBudgetItems(Request $request){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.por_lineas_presupuesto',
            'module' => $this->module,
            'slug' => 'orders'
        ];   

        if(count($request->all())){
            $with_query = true;
            $companies_ = $request->companies;
            $from_ = $request->from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:date('Y-m-d');
            $to_ = $request->to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:date('Y-m-d');
            $status_ = $request->status? $request->status:'all';

            //Ventas o compras:
            $order_type = $request->order_type;

        }else{
            $with_query = false;
            $companies_ = [];
            $from_ = date('Y-m-d');
            $to_ = date('Y-m-d');
            $status_ = 'all';

            //Ventas o compras:
            $order_type = 'v';
        }

        //Empresas vinculadas al usuario:
        $user_companies = $this->getUserCompanies(Auth::user()->id);

        //Estados de presupuesto:
        $budget_status = helpers::budgetStatus();

        $languages = $this->languages;   

        return view('admin.order_queries.by-budget-items')->with(compact('header', 'with_query', 'companies_', 'from_', 'to_', 'order_type', 'status_', 'user_companies', 'budget_status', 'languages'));    
    }

    /**
     * 5.1. Consulta items de presupuestos para DataTables.
     */
    public function getBudgetItemsDT(Request $request, $companies, $order_type, $status, $from = false, $to = false){
        if($request->ajax()){
            $companies = $companies? explode(',', $companies):[];
            $from = $from? date("Y-m-d", $from):false;
            $to = $to? date("Y-m-d", $to):false;

            $budget_status = helpers::budgetStatus();

            $data = Item::select('items.id', 'items.order_id', 'items.concept', 'items.qty', 'items.price', 'items.iva', 'items.irpf', 'items.discount', 'items.days', 'items.days_continue', 'items.days_continue_discount', 'p.name AS product', 'pp.name AS purchase_product', 'bfi.name AS budget_form_block', 'o.is_order', 'o.is_budget', 'o.invoiced', 'o.order_num', 'o.order_old_num', 'o.budget_num', 'o.budget_old_num', 'o.name AS order_name', 'o.accepted_date', 'o.delivery_date', 'o.bag_amount', 'o.created_at', 'o.budget_status', 'cp.name AS provider', 'cc.name AS customer', 'cfc.name AS final_customer', 'up.name AS pm_name', 'up.surname AS pm_surname', 'ucom.name AS com_name', 'ucom.surname AS com_surname', 'uc.name AS cm_name', 'uc.surname AS cm_surname', 'ufc.name AS fcm_name', 'ufc.surname AS fcm.surname', 'pcc.name AS p_cost_center', 'ccc.name AS c_cost_center')
            ->join('orders AS o', 'items.order_id', '=', 'o.id')
            ->leftJoin('products AS p', 'items.product_id', '=', 'p.id')
            ->leftJoin('purchase_products AS pp', 'items.purchase_product_id', '=', 'pp.id')
            ->leftJoin('budget_form_items AS bfi', 'items.budget_form_block_id', '=', 'bfi.id')
            ->join('companies AS cp', 'o.provider_id', '=', 'cp.id')
            ->join('companies AS cc', 'o.customer_id', '=', 'cc.id')    
            ->leftJoin('companies AS cfc', 'o.final_customer_id', '=', 'cfc.id')
            ->leftJoin('users AS up', 'o.provider_manager_id', '=', 'up.id')
            ->leftJoin('users AS ucom', 'o.commercial_id', '=', 'ucom.id')
            ->leftJoin('users AS uc', 'o.customer_manager_id', '=', 'uc.id')
            ->leftJoin('users AS ufc', 'o.final_customer_manager_id', '=', 'ufc.id')
            ->leftJoin('cost_centers AS pcc', 'items.provider_cost_center_id', '=', 'pcc.id')
            ->leftJoin('cost_centers AS ccc', 'items.customer_cost_center_id', '=', 'ccc.id')
            ->where('o.is_budget', true)
            ->whereNull('items.deleted_at')
            // Intervalo fechas (por fecha de creación):
            ->when($from && $to, function($q) use($from, $to){
                return $q->whereBetween('o.created_at', [$from, $to]);
            })
            //Ventas:
            ->when($order_type == 'v', function($q) use($companies){
                return $q->whereIn('o.provider_id', $companies);
            })
            //Compras:
            ->when($order_type == 'c', function($q) use($companies){
                return $q->whereIn('o.customer_id', $companies);
            })
            //Estado:
            ->when($status && $status != 'all', function($q) use($status){
                return $q->where('o.budget_status', $status);
            })
            ->orderBy('o.id', 'ASC');

            //Tabla:
            return DataTables::of($data)
            ->addColumn('provider', function($row){
                return $row->provider; 
            })
            ->addColumn('customer', function($row){
                return $row->customer; 
            })
            ->addColumn('final_customer', function($row){
                return $row->final_customer; 
            })
            ->addColumn('is_order', function($row){
                if($row->is_order == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('is_budget', function($row){
                if($row->is_budget == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('budget', function($row){
                $budget = $row->budget_num? $row->budget_num:($row->budget_old_num? $row->budget_old_num:$row->order_id);
                return '<a target="_blank" href="/admin/budgets/'.$row->order_id.'/edit">'.$budget.'</a>'; 
            })
            ->addColumn('order', function($row){
                $order = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->order_id);
                return '<a target="_blank" href="/admin/orders/'.$row->order_id.'/edit">'.$order.'</a>'; 
            })
            ->addColumn('order_name', function($row){
                return $row->order_name; 
            })
            ->addColumn('created_at', function($row){
                return $row->created_at->format($this->languages[session()->get('locale')][4]);
            })
            ->addColumn('accepted', function($row){
                return $row->accepted_date? Carbon::parse($row->accepted_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('delivered', function($row){
                return $row->delivery_date? Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('status', function($row) use($budget_status){
                if($row->budget_status && isset($budget_status[$row->budget_status])){
                    return ucfirst($budget_status[$row->budget_status]);
                }else{
                    return '';
                }
            })
            ->addColumn('invoiced', function($row){
                if($row->invoiced == 1){
                    return '<span style="display: none">1</span><i class="fa fa-check text-success"></i>';
                }
            })
            ->addColumn('provider_manager', function($row){
                if($row->pm_name){
                    return $row->pm_name.' '.$row->pm_surname;
                }
            })
            ->addColumn('commercial', function($row){
                if($row->com_name){
                    return $row->com_name.' '.$row->com_surname;
                }
            })
            ->addColumn('customer_manager', function($row){
                if($row->cm_name){
                    return $row->cm_name.' '.$row->cm_surname;
                }
            })
            ->addColumn('final_customer_manager', function($row){
                if($row->fcm_name){
                    return $row->fcm_name.' '.$row->fcm_surname;
                }
            })
            ->addColumn('bag', function($row){
                if($row->bag_amount > 0){
                    $row->bag_amount;
                }
            })
            ->addColumn('product', function($row){
                return $row->product;
            })
            ->addColumn('concept', function($row){
                return $row->concept;
            })
            ->addColumn('budget_form_block', function($row){
                return $row->budget_form_block;
            })
            ->addColumn('accounting_product', function($row){
                return $row->purchase_product;
            })
            ->addColumn('qty', function($row){
                return $row->qty;
            })
            ->addColumn('price', function($row){
                return $row->price;
            })
            ->addColumn('iva', function($row){
                return $row->iva;
            })
            ->addColumn('irpf', function($row){
                return $row->irpf;
            })
            ->addColumn('discount', function($row){
                if($row->discount > 0){
                    return $row->discount.'%';
                }
            })
            ->addColumn('by_percent', function($row){

            })
            ->addColumn('days', function($row){
                return $row->days;
            })
            ->addColumn('days_continue', function($row){
                return $row->days_continue;
            })
            ->addColumn('days_continue_discount', function($row){
                if($row->days_continue_discount > 0){
                    return $row->days_continue_discount.'%';
                }
            })
            ->addColumn('amount', function($row){
                return round(Item::getItemAmount($row), 2);
            })
            ->addColumn('provider_cost_center', function($row){
                return $row->p_cost_center;
            })
            ->addColumn('customer_cost_center', function($row){
                return $row->c_cost_center;
            })
            ->rawColumns(['is_order', 'is_budget', 'invoiced', 'budget', 'order'])
            ->make(true);
        }
    }

    /**
     * 6. Setear centro de coste de proveedor para líneas NULL.
     */
    public function setProviderCostCenter(){
        $data = Item::select('id', 'product_id', 'provider_cost_center_id')
        ->whereNotNull('product_id')
        ->whereNotNull('provider_cost_center_id')
        ->groupBy('product_id')
        ->get();

        // $sin = Item::select('id', 'product_id', 'provider_cost_center_id')
        // ->whereNotNull('product_id')
        // ->whereNull('provider_cost_center_id')
        // ->get();

        foreach($data as $r){
            Item::where('product_id', $r->product_id)
            ->whereNull('provider_cost_center_id')
            ->update(['provider_cost_center_id' => $r->provider_cost_center_id]);
        }

        $data_ = Item::select('items.id', 'items.product_id', 'products.cost_center_id')
        ->join('products', 'items.product_id', '=', 'products.id')
        ->whereNull('items.provider_cost_center_id')
        ->whereNotNull('products.cost_center_id')
        ->groupBy('items.product_id')
        ->get();

        foreach($data_ as $r_){
            Item::where('product_id', $r_->product_id)
            ->whereNull('provider_cost_center_id')
            ->update(['provider_cost_center_id' => $r->cost_center_id]);
        }
    }
    
}
