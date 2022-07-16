<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\CompanySettings;
use App\Models\Product;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderPatterns;

class WorkOrderController extends Controller{
    /**
     * 1. Órdenes de trabajo.
     * 1.1. Listado de órdenes de trabajo para DataTable.
     * 2. Acción de creación de orden de trabajo.
     * 3. Editar orden de trabajo.
     * 4. Actualizar orden de trabajo.
     * 5. Eliminar orden de trabajo.
     * 6. Actualizar estado de orden de trabajo.
     */
    
    private $option = 'produccion';
    private $module = 'production';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:production');
        $this->middleware('work_order_get', ['only' => ['edit', 'update', 'destroy']]);
    }    

    /**
     * 1. Órdenes de trabajo.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.trabajo_ordenes_',
            'module' => $this->module,
            'slug' => 'work-orders'
        ];    

        return view('admin.work_orders.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de órdenes de trabajo para DataTable.
     */
    public function getWorkOrdersDT(){
        $data = WorkOrder::where('company_id', session('currentCompany'))
        ->orderBy('created_at', 'DESC')
        ->get();

        $arrEstados = Helpers::workOrderStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        //Estados de orden de trabajo aptos para poder eliminar:
        $deleteWorkOrder = ['0', 1];

        //Permisos del usuario:
        $editar = Auth::user()->can('work-orders.edit');
        $eliminar = Auth::user()->can('work-orders.destroy');

        foreach($data as $row){
            //Enviamos las fechas ya formateadas:
            $row->fechaEntregaPrevista = Carbon::parse($row->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]);
            $row->fechaEntrega = Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]);
            $row->alta = $row->created_at? $row->created_at->format($this->languages[session()->get('locale')][4]):'';

            //Estado de la orden de trabajo:
            $row->estado = $row->status? ucfirst($arrEstados[$row->status]):'';
            $row->estados = $row->status.';'.$strEstados;

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;

            //Orden de trabajo no se puede eliminar según su status:
            $row->allow_delete = in_array($row->status, $deleteWorkOrder)? true:false;
        }

        return datatables($data)->toJson();
    }

    /**
     * 2. Acción de creación de orden de trabajo.
     *
     * Creamos directamente la orden y se redirige hacia edición.
     */
    public function create(){
        $row = new WorkOrder();
        $row->company_id = session('currentCompany');
        $row->created_by = Auth::user()->id;
        $row->updated_by = Auth::user()->id;
        $row->save();

        $msg = trans('textos.trabajo_orden_creada');
        return redirect()->route('work-orders.edit', $row)->with(compact('msg'));
    }

    /**
     * 3. Editar orden de trabajo.
     */
    public function edit(WorkOrder $work_order, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.trabajo_orden_editar',
            'module' => $this->module,
            'slug' => 'work-orders'
        ]; 

        $patterns = WorkOrderPatterns::where('company_id', session('currentCompany')) 
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();  

        $author = User::select('id', 'name', 'surname')->find($work_order->created_by);

        //Productos. Activos y aptos para venta:
        $products = Product::companyProducts($work_order->company_id, $work_order->company_id);

        //Cambios bloqueados: establecer las condiciones por las que se pueden realizar ciertas acciones o no.
        $permitted = true;

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $languages = $this->languages;

        return view('admin.work_orders.edit')->with(compact('header', 'work_order', 'tab', 'patterns', 'author', 'products', 'permitted', 'currency', 'languages'));
    }

    /**
     * 4. Actualizar orden de trabajo.
     */
    public function update(Request $request, WorkOrder $work_order){
        //Número de orden de trabajo:
        $work_order_num = NULL;
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('work_order', $request->pattern_id);
        }

        //Slug:
        $slug = Str::slug($request->name);

        //Formateando fechas:
        $estimated_delivery_date = $request->estimated_delivery_date != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->estimated_delivery_date):$request->estimated_delivery_date:NULL;
        $delivery_date = $request->delivery_date != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->delivery_date):$request->delivery_date:NULL; 

        //Updating:
        if($request->pattern_id){
            if(!empty($patternNum)){
                $work_order->work_order_num = $patternNum['pattern_num'];  
                $work_order->num_cardinal = $patternNum['num']; 
            }            
            $work_order->pattern_id = $request->pattern_id;
        }
        $work_order->work_order_old_num = $request->work_order_old_num;
        $work_order->name = $request->name;
        $work_order->slug = $slug;
        $work_order->description = $request->description;
        $work_order->estimated_delivery_date = $estimated_delivery_date;
        $work_order->delivery_date = $delivery_date;
        $work_order->status = $request->status;
        $work_order->updated_by = Auth::user()->id;
        $work_order->simulation = $request->simulation? true:false;
        $work_order->save();

        $msg = trans('textos.trabajo_orden_updated');
        return redirect()->route('work-orders.edit', $work_order)->with(compact('msg'));
    }

    /**
     * 5. Eliminar orden de trabajo.
     */
    public function destroy(WorkOrder $work_order){
        $work_order->delete(); 
        
        $msg = trans('textos.trabajo_orden_deleted');
        return redirect()->route('work-orders.index')->with(compact('msg'));    
    }

    /**
     * 6. Actualizar estado de orden de trabajo.
     */
    public function status(Request $request, WorkOrder $work_order, $status){
        if($request->ajax()){    
            $work_order->status = $status;
            $work_order->save();

            return response()->json(true);
        }
    }
}
