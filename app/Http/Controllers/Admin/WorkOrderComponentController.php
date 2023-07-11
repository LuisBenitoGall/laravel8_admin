<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\WorkOrder;
use App\Models\WorkOrderComponent;

class WorkOrderComponentController extends Controller{
    /**
     * 1. Guardar componente para orden de trabajo.
     * 2. Obtener componentes de orden de trabajo para DataTable.
     * 3. Eliminar componente de orden de trabajo.
     */
    
    private $option = 'produccion';
    private $module = 'production';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:production');
        $this->middleware('work_order_post', ['only' => ['store']]);
        $this->middleware('work_order_product_post', ['only' => ['update']]);
        $this->middleware('work_order_component_get', ['only' => ['destroy', 'status']]);
    }   

    //0. Validator:
    protected function validator(array $data){
        //Validación save: 
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'work_order_id' => 'required|integer',
                'product_id' => 'required|integer',
                'unit_id' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer'
            ]);    
        }
    }

    /**
     * 1. Guardar componente para orden de trabajo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-component');

            return redirect('/admin/work-orders/'.$request->work_order_id.'/edit/products')
                ->withErrors($validator)
                ->withInput();
        }

        $row = new WorkOrderComponent();
        $row->work_order_id = $request->work_order_id;
        $row->product_id = $request->product_id; 
        $row->supplied_qty = $request->supplied_qty > 0? $request->supplied_qty:1;
        $row->unit_id = $request->unit_id;
        $row->created_by = Auth::user()->id;
        $row->observations = $request->observations;
        $row->status = 1;       //Por defecto: pendiente (1)
        $row->save();

        $msg = trans('textos.componente_agregado');
        return redirect()->route('work-orders.edit', [$request->work_order_id, 'components'])->with(compact('msg'));
    }

    /**
     * 2. Obtener componentes de orden de trabajo para DataTable.
     */
    public function getComponentsDT(WorkOrder $work_order){
        $data = WorkOrderComponent::select('work_order_components.id', 'work_order_components.supplied_qty', 'work_order_components.used_qty', 'work_order_components.returned_qty', 'work_order_components.observations', 'work_order_components.status', 'products.id AS productId', 'products.name', 'units.symbol')    
        ->join('products', 'work_order_components.product_id', '=', 'products.id')
        ->leftJoin('units', 'work_order_components.unit_id', '=', 'units.id')
        ->where('work_order_components.work_order_id', $work_order->id)
        ->orderBy('products.name', 'ASC');

        $estados = helpers::WorkOrderComponentStatus();

        return DataTables::of($data)
        ->addColumn('product', function($row){
            return '<a target="_blank" href="/admin/products/'.$row->productId.'/show">'.$row->name.'</a>';
        })
        ->addColumn('supplied_qty', function($row){
            if($row->supplied_qty > 0){
                return $row->supplied_qty.$row->symbol; 
            }
        })
        ->addColumn('used_qty', function($row){
            if($row->used_qty > 0){
                return $row->used_qty.$row->symbol; 
            }
        })
        ->addColumn('returned_qty', function($row){
            if($row->returned_qty > 0){
                return $row->returned_qty.$row->symbol; 
            }
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.work_order_products.partials.dt-status', compact('row', 'estados'));
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.work_order_components.partials.dt-actions', compact('row', 'estados'));    
        })
        ->rawColumns(['product'])
        ->make(true);
    }

    /**
     * 3. Eliminar componente de orden de trabajo.
     *
     * @param  str $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrderComponent $component){
        $component->delete();

        $msg = trans('textos.componente_deleted');
        return redirect()->route('work-orders.edit', [$component->work_order_id, 'components'])->with(compact('msg'));    
    }
}
