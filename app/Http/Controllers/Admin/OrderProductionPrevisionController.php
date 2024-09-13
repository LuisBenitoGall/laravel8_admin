<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\Order;
use App\Models\OrderProductionPrevision;
use App\Models\Workplace;

class OrderProductionPrevisionController extends Controller{
    /**
     * 1. Eliminar previsión de producción.
     * 2. Copiar registros de order_previsions.
     * 3. Panel de control de producción.
     * 3.1. Filtro panel de control.
     * 3.2. Listado de previsiones para DataTable.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';

    public function __construct(){
        $this->middleware('module_setted:orders');
        //$this->middleware('order_get', ['only' => ['recalculatePrevisions']]);
    }

    /**
     * 1. Eliminar previsión de producción.
     */
    public function destroy(Request $request, OrderProductionPrevision $prevision){
        if($request->ajax()){
            $prevision->forceDelete();

            return response()->json(true);
        }
    }

    /**
     * 2. Copiar registros de order_previsions.
     */
    public function copy(){
        OrderProductionPrevision::copyRegisters();
    }

    /**
     * 3. Panel de control de producción.
     */
    public function controlPanel($year = false, $workplace_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.produccion_panel_control',
            'module' => $this->module,
            'slug' => 'control-panel'
        ];   
        
        $year = !$year? Date('Y'):$year;

        $min_year = config('constants.MIN_YEAR_');
        $max_year = date('Y') + 1;

        //Centros de trabajo:
        $workplaces = Workplace::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $workplaces[''] = trans('textos.todos');
        ksort($workplaces);     //Reordenamos array.

        return view('admin.order_production_previsions.index')->with(compact('header', 'year', 'workplace_id', 'min_year', 'max_year', 'workplaces'));  
    }

    /**
     * 3.1. Filtro panel de control.
     */
    public function filterControlPanel(Request $request){
        return redirect()->route('order-production-prevision.control-panel', [$request->year, $request->workplace]);
    }

    /**
     * 3.2. Listado de previsiones para DataTable.
     */
    public function getPrevisionsDT($year, $workplace_id = false){
        //Estados de pedido que quedan fuera del panel: pedidos cancelados,...
        $statusRejected = config('constants.ORDER_STATUS_NOT_INVOICED_');

        $estados = helpers::orderStatus();

        $data = Order::select('orders.id', 'orders.name', 'orders.order_num', 'orders.order_old_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.is_group', 'orders.estimated_delivery_date', 'orders.delivery_date', 'orders.order_status', 'orders.invoiced', 'orders.discount', 'orders.bag_amount', 'order_production_previsions.year', 'order_production_previsions.month', 'order_production_previsions.amount', 'order_production_previsions.percent', 'orders.created_at', 'companies.name AS Customer', 'users.name AS nombre', 'users.surname', 'cc.name AS finalCustomer', 'u_.name AS CustomerManagerName', 'u_.surname AS CustomerManagerSurname', 'workplaces.name AS workplace')
        ->join('order_production_previsions', 'orders.id', '=', 'order_production_previsions.order_id')
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->leftJoin('companies AS cc', 'orders.final_customer_id', '=', 'cc.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->leftJoin('users AS u_', 'orders.customer_manager_id', '=', 'u_.id')
        ->leftJoin('workplaces', 'orders.provider_workplace_id', '=', 'workplaces.id')
        ->where('orders.provider_id', session('currentCompany'))
        ->where('orders.is_order', 1)
        ->whereNull('orders.order_parent')
        ->whereNull('order_production_previsions.deleted_at')
        ->where('order_production_previsions.year', $year)
        ->whereNotIn('orders.order_status', $statusRejected)
        ->when($workplace_id, function($q) use($workplace_id){
            return $q->where('orders.provider_workplace_id', $workplace_id);
        })
        ->orderBy('orders.id', 'DESC')
        ->orderBy('orders.created_at', 'DESC');

        return DataTables::of($data)
        ->addColumn('fecha', function($row){
            $month = $row->month && in_array($row->month, ['01','02','03','04','05','06','07','08','09','10','11','12'])? helpers::yearMonths($row->month):'';
            return '<span style="display: none">'.strtotime($row->year.'-'.$row->month.'-01').'</span>'.$month.' '.$row->year;
        })
        ->filterColumn('fecha', function($q, $keyword){
            $sql = "order_production_previsions.year like ?";
            $q->whereRaw($sql, ["%$keyword%"]);
        })
        ->addColumn('customer', function($row){
            return $row->Customer;
        })
        ->addColumn('order_num', function($row){
            $order_num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
            return '<a target="_blank" href="'.route('orders.edit', $row->id).'">'.$order_num.'</a>';
        })   
        ->addColumn('project', function($row){
            $projects = $row->projects;
            if($projects->count()){
                $projects_ = '';
                foreach ($projects as $project){
                    $num_ = $project->project_num ?? $project->id;
                    $projects_ .= '<a href="/admin/projects/'.$project->id.'/edit">'.$num_.'</a> ';
                }
                return $projects_;
            }else{
                return '';
            }
        })
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('manager', function($row){
            if($row->nombre){
                return $row->nombre.' '.$row->surname;
            }else{
                return '';
            }
        })
        ->addColumn('workplace', function($row){
            return $row->workplace;
        })
        ->addColumn('amount', function($row){
            return round($row->amount, 2);
        })  
        ->addColumn('purchase_num', function($row){
            $purchase_num = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:'');
            return $purchase_num;
        })
        ->addColumn('customer_manager', function($row){
            if($row->CustomerManagerName){
                return $row->CustomerManagerName.' '.$row->CustomerManagerSurname;
            }else{
                return '';
            }
        })
        ->addColumn('estado', function($row) use($estados){ 
            if(isset($estados[$row->order_status])){
                return ucfirst($estados[$row->order_status]);
            }else{
                return '';
            }
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.order_production_previsions.partials.dt-actions', compact('row', 'estados'));    
        })
        ->rawColumns(['fecha', 'order_num', 'project'])
        ->orderColumn('fecha', 'customer')
        ->make(true);
    }

}
