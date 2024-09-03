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
use App\Models\Invoice;

class InvoiceQueryController extends Controller{
    /**
     * 1. Listado de consultas.
     * 2. Por facturas.
     * 2.1. Consulta de facturas para DataTables.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:accounting');
    }

    /**
     * 1. Listado de consultas.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.consultas',
            'module' => $this->module,
            'slug' => 'invoice-queries'
        ];  

        //Relación de consultas:
        $queries = [
            ['por_facturas', 'by-invoices']
        ];

        return view('admin.invoice_queries.index')->with(compact('header', 'queries'));
    }

    /**
     * 2. Por facturas.
     */
    public function byInvoices(Request $request){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.por_facturas',
            'module' => $this->module,
            'slug' => 'invoice-queries'
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

        //Estados de factura:
        $invoice_status = helpers::invoiceStatus();

        $languages = $this->languages;   

        return view('admin.invoice_queries.by-invoices')->with(compact('header', 'with_query', 'companies_', 'from_', 'to_', 'status_', 'order_type', 'user_companies', 'invoice_status', 'languages')); 
    }

    /**
     * 2.1. Consulta de facturas para DataTables.
     */
    public function getInvoicesDT(Request $request, $companies, $order_type, $status, $from = false, $to = false){
        if($request->ajax()){
            $companies = $companies? explode(',', $companies):[];
            $from = $from? date("Y-m-d", $from):false;
            $to = $to? date("Y-m-d", $to):false;

            $invoice_status = helpers::invoiceStatus();

            $data = Invoice::select('invoices.id', 'invoices.refund', 'invoices.invoice_num', 'invoices.invoice_old_num', 'invoices.amount', 'invoices.amount_with_iva', 'invoices.fiscal_date', 'invoices.invoice_date', 'invoices.expiration_date', 'invoices.status', 'orders.name AS order_title', 'orders.budget_num', 'orders.budget_old_num', 'orders.order_num', 'orders.order_old_num', 'orders.accepted_date', 'orders.delivery_date', 'payment_methods.name AS method', 'cp.name AS provider', 'cp.nif AS cp_nif', 'cc.name AS customer', 'cc.nif AS cc_nif', 'cfc.name AS final_customer', 'up.name AS pm_name', 'up.surname AS pm_surname', 'ucom.name AS com_name', 'ucom.surname AS com_surname', 'uc.name AS cm_name', 'uc.surname AS cm_surname', 'ufc.name AS fcm_name', 'ufc.surname AS fcm.surname', 'ccp.name AS ccp_name', 'ccc.name AS ccc_name')
            ->join('orders', 'invoices.order_id', '=', 'orders.id')
            ->join('companies AS cp', 'orders.provider_id', '=', 'cp.id')
            ->join('companies AS cc', 'orders.customer_id', '=', 'cc.id')
            ->leftJoin('companies AS cfc', 'orders.final_customer_id', '=', 'cfc.id')
            ->leftJoin('users AS up', 'orders.provider_manager_id', '=', 'up.id')
            ->leftJoin('users AS ucom', 'orders.commercial_id', '=', 'ucom.id')
            ->leftJoin('users AS uc', 'orders.customer_manager_id', '=', 'uc.id')
            ->leftJoin('users AS ufc', 'orders.final_customer_manager_id', '=', 'ufc.id')
            ->leftJoin('cost_centers AS ccp', 'orders.provider_cost_center_id', '=', 'ccp.id')
            ->leftJoin('cost_centers AS ccc', 'orders.customer_cost_center_id', '=', 'ccc.id')
            ->leftJoin('payment_methods', 'invoices.payment_method_id', '=', 'payment_methods.id')
            ->whereNull('invoices.deleted_at')
            //Intervalo fechas (por fecha de creación):
            ->when($from && $to, function($q) use($from, $to){
                //return $q->whereBetween('invoices.created_at', [$from, $to]);
                //03/09/2024: se modifica el campo para el filtro de fecha:
                return $q->whereBetween('invoices.fiscal_date', [$from, $to]);
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
                return $q->where('invoices.status', $status);
            })
            ->orderBy('invoices.id', 'ASC');

            //Tabla:
            return DataTables::of($data)
            ->addColumn('invoice', function($row){
                $invoice = $row->invoice_num? $row->invoice_num:($row->invoice_old_num? $row->invoice_old_num:$row->id);
                return $invoice; 
            })
            ->addColumn('provider', function($row){
                return $row->provider; 
            })
            ->addColumn('provider_nif', function($row){
                return $row->cp_nif; 
            })
            ->addColumn('customer', function($row){
                return $row->customer; 
            })
            ->addColumn('customer_nif', function($row){
                return $row->cc_nif; 
            })
            ->addColumn('order', function($row){
                $order = $row->order_num? $row->order_num:$row->order_old_num;
                return $order; 
            })
            ->addColumn('budget', function($row){
                $budget = $row->budget_num? $row->budget_num:$row->budget_old_num;
                return $budget; 
            })
            ->addColumn('title', function($row){
                return $row->order_title; 
            })
            ->addColumn('amount', function($row){
                if($row->refund){
                    return $row->amount * (-1);
                }else{
                    return $row->amount; 
                }
            })
            ->addColumn('amount_iva', function($row){
                if($row->refund){
                    return $row->amount_with_iva * (-1);
                }else{
                    return $row->amount_with_iva; 
                } 
            })
            ->addColumn('accepted_date', function($row){
                return $row->accepted_date? Carbon::parse($row->accepted_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('delivery_date', function($row){
                return $row->delivery_date? Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('invoice_date', function($row){
                return $row->invoice_date? Carbon::parse($row->invoice_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('fiscal_date', function($row){
                return $row->fiscal_date? Carbon::parse($row->fiscal_date)->format($this->languages[session()->get('locale')][4]):'';
            })
            ->addColumn('expiration_date', function($row){
                return $row->expiration_date? Carbon::parse($row->expiration_date)->format($this->languages[session()->get('locale')][4]):'';
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
            ->addColumn('final_customer', function($row){
                if($row->fcm_name){
                    return $row->fcm_name.' '.$row->fcm_surname;
                }
            })
            ->addColumn('final_customer', function($row){
                return $row->final_customer; 
            })
            ->addColumn('final_customer_manager', function($row){
                if($row->fcm_name){
                    return $row->fcm_name.' '.$row->fcm_surname;
                }
            })
            ->addColumn('provider_cost_center', function($row){
                return $row->ccp_name; 
            })
            ->addColumn('customer_cost_center', function($row){
                return $row->ccc_name; 
            })
            ->addColumn('status', function($row) use($invoice_status){
                if($row->status && isset($invoice_status[$row->status])){
                    return ucfirst($invoice_status[$row->status]);
                }else{
                    return '';
                }
            })
            ->addColumn('payment_method', function($row){
                if($row->method){
                    return $row->method;
                }else{
                    return '';
                }     
            })
            ->make(true);
        }    
    }
}
