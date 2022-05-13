<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\WorkOrderProducts;

class WorkOrderProductsController extends Controller{
    /**
     * 1. Guardar producto para orden de trabajo.
     * 2. Obtener productos de orden de trabajo para DataTable.
     * 3. Eliminar producto de orden de trabajo.
     * 4. Actualizar estado de producto de orden de trabajo.
     * 5. Actualizar producto para orden de trabajo.
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
        $this->middleware('work_order_product_get', ['only' => ['destroy', 'status']]);
    }   

    //0. Validator:
    protected function validator(array $data){
        //Validación save: 
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'work_order_id' => 'required|integer',
                'product_id' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'product_id' => 'required|integer'
            ]);    
        }
    }

    /**
     * 1. Guardar producto para orden de trabajo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/work-orders')
                ->withErrors($validator)
                ->withInput();
        }

        $row = new WorkOrderProducts();
        $row->work_order_id = $request->work_order_id;
        $row->product_id = $request->product_id; 
        $row->planned_qty = $request->planned_qty > 0? $request->planned_qty:1;
        $row->observations = $request->observations;
        $row->with_batch = $request->with_batch? true:false;
        $row->save();

        $msg = trans('textos.producto_agregado');
        return redirect()->route('work-orders.edit', [$request->work_order_id, 'products'])->with(compact('msg'));
    }

    /**
     * 2. Obtener productos de orden de trabajo para DataTable.
     */
    public function getProductsDT($work_order){
        $data = WorkOrderProducts::select('work_order_products.id', 'work_order_products.planned_qty', 'work_order_products.produced_qty', 'work_order_products.observations', 'work_order_products.status', 'products.id AS productId', 'products.name')    
        ->join('products', 'work_order_products.product_id', '=', 'products.id')
        ->where('work_order_products.work_order_id', $work_order)
        ->orderBy('products.name', 'ASC')
        ->get(); 

        $arrEstados = Helpers::workOrderProductStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        //Estados de orden de trabajo aptos para poder eliminar:
        $deleteWorkOrderProduct = ['0', 1];

        //Permisos del usuario:
        $editar = Auth::user()->can('work-orders.edit');

        foreach($data as $row){
            //Estado de la orden de trabajo:
            $row->estado = $row->status? ucfirst($arrEstados[$row->status]):'';
            $row->estados = $row->status.';'.$strEstados;

            //Permisos para las acciones:
            $row->editar = $editar;

            //El producto no se puede eliminar según el estado:
            $row->allow_delete = in_array($row->status, $deleteWorkOrderProduct)? true:false;
        }

        return datatables($data)->toJson();
    }

    /**
     * 3. Eliminar producto de orden de trabajo.
     *
     * @param  str $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkOrderProducts $product){
        $product->delete();

        $msg = trans('textos.producto_deleted');
        return redirect()->route('work-orders.edit', [$product->work_order_id, 'products'])->with(compact('msg'));    
    }

    /**
     * 4. Actualizar estado de producto de orden de trabajo.
     */
    public function status(Request $request, WorkOrderProducts $product, $status){
        if($request->ajax()){    
            $product->status = $status;
            $product->save();

            return response()->json(true);
        }
    }

    /**
     * 5. Actualizar producto para orden de trabajo.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/work-orders/'.$request->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $row = WorkOrderProducts::find($request->id);
        $row->product_id = $request->product_id; 
        $row->planned_qty = $request->planned_qty > 0? $request->planned_qty:1;
        $row->observations = $request->observations;
        //$row->with_batch = $request->with_batch? true:false;
        $row->save();

        $msg = trans('textos.producto_updated');
        return redirect()->route('work-orders.edit', [$row->work_order_id, 'products'])->with(compact('msg'));
    }
}
