<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\Batch;
use App\Models\BatchPatterns;
use App\Models\Product;

class BatchController extends Controller{
    /**
     * 1. Lotes.
     * 1.1. Listado de lotes para DataTable.
     * 2. Lotes por producto.
     * 3. Crear lote.
     * 4. Editar lote.
     * 5. Actualizar lote.
     */
    
    private $option = 'lotes';  
    private $module = 'production';
    private $languages;

    //0. Validator:
    protected function validator(array $data){
        //Validación update:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required'
            ]); 
        }        
    }

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:production');
    }

    /**
     * 1. Lotes.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.lotes',
            'module' => $this->module,
            'slug' => 'batches'
        ]; 

        return view('admin.batches.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de lotes para DataTable.
     */
    public function getBatchesDT(){
        $data = Batch::select('batches.id', 'batches.name', 'batches.deadline', 'batches.expiry_date', 'batches.initial_qty', 'batches.doc_num', 'batches.doc', 'batches.created_at', 'products.name AS product', 'work_orders.id AS work_order_id', 'work_orders.work_order_num')
        ->join('products', 'batches.product_id', '=', 'products.id')
        ->leftJoin('work_orders', 'batches.work_order_id', '=', 'work_orders.id')
        ->where('batches.name', '!=', config('constants.NOBATCH_'))
        ->where('products.company_id', session('currentCompany'));

        return DataTables::of($data)
        ->addColumn('batch', function($row){
            return $row->name;
        })
        ->addColumn('product', function($row){
            return $row->product;
        })
        ->addColumn('fecha', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('expiry_date', function($row){
            if($row->expiry_date){
                return Carbon::parse($row->expiry_date)->format($this->languages[session()->get('locale')][4]);   
            }else{
                return '';
            }
        })
        ->addColumn('work_order', function($row){
            if($row->work_order_id){
                return '<a target="_blank" href="'.route('work-orders.edit', $row->work_order_id).'">'.$row->work_order_num.'</a>';
            }else{
                return '';
            }
        })
        ->addColumn('initial_qty', function($row){
            return $row->initial_qty;
        })
        ->addColumn('acciones', function($row){
            return view('admin.batches.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['work_order'])
        ->make(true);
    }

    /**
     * 2. Lotes por producto.
     */
    public function productBatches(Request $request, $product_id){
        if($request->ajax()){
            $data = Batch::select('id', 'name')
            ->where('product_id', $product_id)
            ->groupBy('id')
            ->get();

            return response()->json($data);
        }
    }

    /**
     * 3. Crear lote.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.lotes_crear',
            'module' => $this->module,
            'slug' => 'batches'
        ]; 

        $products = Product::where('company_id', session('currentCompany'))
        ->where('type', 'p')
        ->where('status', 1)
        ->where('batch', 1)
        ->pluck('name', 'id')
        ->toArray();

        $patterns = BatchPatterns::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $languages = $this->languages;

        return view('admin.batches.create')->with(compact('header', 'products', 'patterns', 'languages')); 
    }

    /**
     * 4. Editar lote.
     */
    public function edit(Batch $batch){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.lotes_editar',
            'module' => $this->module,
            'slug' => 'batches'
        ]; 

        $languages = $this->languages;

        return view('admin.batches.edit')->with(compact('header', 'batch', 'languages'));    
    }

    /**
     * 5. Actualizar lote.
     */
    public function update(Request $request, Batch $batch){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/batches/'.$batch->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }  



        $msg = trans('textos.lote_updated');
        return redirect()->route('batches.edit', [$batch->id, false])->with(compact('msg'));
    }
}
