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
use App\Models\UserCompanies;

class OrderQueryController extends Controller{
    /**
     * 1. Listado de consultas.
     * 2. Por líneas de pedido.
     * 2.1. Consulta items de pedidos para DataTables.
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
            ['por_lineas_pedido', 'by-items']
        ];

        return view('admin.order_queries.index')->with(compact('header', 'queries'));
    }

    /**
     * 2. Por líneas de pedido.
     */
    public function byItems(Request $request){
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

            //Ventas o compras:
            $order_type = $request->order_type;

        }else{
            $with_query = false;
            $companies_ = [];
            $from_ = date('Y-m-d');
            $to_ = date('Y-m-d');

            //Ventas o compras:
            $order_type = 'v';
        }



        //Empresas vinculadas al usuario:
        $user_companies = UserCompanies::select('companies.id', 'companies.name')
        ->join('companies', 'user_companies.company_id', '=', 'companies.id')
        ->where('user_companies.user_id', Auth::user()->id)
        ->pluck('name', 'id')
        ->toArray();

        //Estados de pedido:
        $order_status = helpers::orderStatus();

        $languages = $this->languages;   

        return view('admin.order_queries.by-items')->with(compact('header', 'with_query', 'companies_', 'from_', 'to_', 'order_type', 'user_companies', 'order_status', 'languages'));    
    }

    /**
     * 2. Consulta items de pedidos para DataTables.
     */
    public function getOrderItemsDT(Request $request, $companies, $order_type, $from = false, $to = false){
        if($request->ajax()){
            $companies = $companies? explode(',', $companies):[];
            $from = $from? date("Y-m-d", $from):false;
            $to = $to? date("Y-m-d", $to):false;

            $order_status = helpers::orderStatus();


            $data = Item::select('items.id', 'items.order_id', 'items.concept', 'items.qty', 'items.price', 'items.iva', 'items.irpf', 'items.discount', 'items.days', 'items.days_continue', 'items.days_continue_discount', 'p.name AS product', 'pp.name AS purchase_product', 'bfi.name AS budget_form_block', 'o.is_order', 'o.invoiced', 'o.order_num', 'o.order_old_num', 'o.budget_num', 'o.budget_old_num', 'o.name AS order_name', 'o.accepted_date', 'o.delivery_date', 'o.bag_amount', 'o.created_at', 'o.order_status', 'cp.name AS provider', 'cc.name AS customer', 'cfc.name AS final_customer', 'up.name AS pm_name', 'up.surname AS pm_surname', 'ucom.name AS com_name', 'ucom.surname AS com_surname', 'uc.name AS cm_name', 'uc.surname AS cm_surname', 'ufc.name AS fcm_name', 'ufc.surname AS fcm.surname', 'pcc.name AS p_cost_center', 'ccc.name AS c_cost_center')
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
            ->leftJoin('cost_centers AS pcc', 'p.cost_center_id', '=', 'pcc.id')
            ->leftJoin('cost_centers AS ccc', 'o.customer_cost_center_id', '=', 'ccc.id')

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
            ->orderBy('o.id', 'ASC');


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
            ->rawColumns(['is_order', 'invoiced', 'budget', 'order'])
            ->make(true);
        }
    }
}
