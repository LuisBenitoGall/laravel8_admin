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
use App\Models\Batch;
use App\Models\WorkOrder;
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
                'store' => 'required|integer',
                'product_id' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer'
            ]);    
        }
    }

    /**
     * 1. Guardar producto para orden de trabajo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-product');

            return redirect('/admin/work-orders/'.$request->work_order_id.'/edit/products')
                ->withErrors($validator)
                ->withInput();
        }

        $row = new WorkOrderProducts();
        $row->work_order_id = $request->work_order_id;
        $row->store_id = $request->store;
        $row->product_id = $request->product_id; 
        $row->planned_qty = $request->planned_qty > 0? $request->planned_qty:1;
        $row->observations = $request->observations;
        $row->with_batch = $request->with_batch? true:false;
        $row->status = 1;       //Por defecto: en espera (1)
        $row->save();

        //Generación de lote:
        if($row->with_batch){
            //Numeración:
            if($request->pattern_id){
                $pattern_id = $request->pattern_id;
                $patternNum = $this->setPatternNumber('batch', $pattern_id);
                $batch_num = $patternNum['pattern_num'];
                $num_cardinal = $patternNum['num'];
            }else{
                $batch_num = NULL;
                $num_cardinal = NULL;
                $pattern_id = NULL;
            }

            $data = [
                'product_id' => $row->product_id,
                'work_order_id' => $row->work_order_id,
                'batch_num' => $batch_num,
                'num_cardinal' => $num_cardinal,
                'pattern_id' => $pattern_id,
                'deadline' => NULL,
                'expiry_date' => NULL,
                'initial_qty' => $row->planned_qty,
                'doc_num' => NULL,
                'doc' => NULL
            ];
            Batch::saveBatch($data);
        }

        $msg = trans('textos.producto_agregado');
        return redirect()->route('work-orders.edit', [$request->work_order_id, 'products'])->with(compact('msg'));
    }

    /**
     * 2. Obtener productos de orden de trabajo para DataTable.
     */
    public function getProductsDT(WorkOrder $work_order){
        $data = WorkOrderProducts::select('work_order_products.id', 'work_order_products.planned_qty', 'work_order_products.produced_qty', 'work_order_products.observations', 'work_order_products.with_batch', 'work_order_products.status', 'products.id AS productId', 'products.name')    
        ->join('products', 'work_order_products.product_id', '=', 'products.id')
        ->where('work_order_products.work_order_id', $work_order->id)
        ->orderBy('products.name', 'ASC');

        $estados = helpers::workOrderProductStatus();

        $batches = $work_order->batches;

        return DataTables::of($data)
        ->addColumn('product', function($row){
            return '<a target="_blank" href="/admin/products/'.$row->productId.'/show">'.$row->name.'</a>';
        })
        ->addColumn('batches', function($row) use($batches){
            $links = '';
            foreach($batches as $b){
                if($b->product_id == $row->productId){
                    $b_num = $b->batch_num? $b->batch_num:$b->id;
                    $links .= '<a target="_blank" href="/admin/batches/'.$b->id.'/edit" class="mr-3">'.$b_num.'</a>';
                }
            }
            return $links;
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.work_order_products.partials.dt-status', compact('row', 'estados'));
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.work_order_products.partials.dt-actions', compact('row', 'estados'));    
        })
        ->rawColumns(['product', 'batches'])
        ->make(true);
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
            $validator->getMessageBag()->add('accion', 'modal-edit-product');

            return redirect('/admin/work-orders/'.$request->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $row = WorkOrderProducts::find($request->id);
        $row->planned_qty = $request->planned_qty > 0? $request->planned_qty:1;
        $row->produced_qty = $request->produced_qty;
        $row->observations = $request->observations;

        //Generación de lote:
        if($request->with_batch){
            //Numeración:
            if($request->pattern_id){
                $pattern_id = $request->pattern_id;
                $patternNum = $this->setPatternNumber('batch', $pattern_id);
                $batch_num = $patternNum['pattern_num'];
                $num_cardinal = $patternNum['num'];
            }else{
                $batch_num = NULL;
                $num_cardinal = NULL;
                $pattern_id = NULL;
            }

            $data = [
                'product_id' => $row->product_id,
                'work_order_id' => $row->work_order_id,
                'batch_num' => $batch_num,
                'num_cardinal' => $num_cardinal,
                'pattern_id' => $pattern_id,
                'deadline' => NULL,
                'expiry_date' => NULL,
                'initial_qty' => $row->planned_qty,
                'doc_num' => NULL,
                'doc' => NULL
            ];
            Batch::saveBatch($data);

            $row->with_batch = true;
        }

        $row->save();

        $msg = trans('textos.producto_updated');
        return redirect()->route('work-orders.edit', [$row->work_order_id, 'products'])->with(compact('msg'));
    }
}
