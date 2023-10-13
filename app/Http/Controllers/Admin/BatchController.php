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
use File;

//Models:
use App\Models\Batch;
use App\Models\BatchPatterns;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovements;
use App\Models\User;
use App\Models\WorkOrder;

class BatchController extends Controller{
    /**
     * 1. Lotes.
     * 1.1. Filtro de lotes.
     * 1.2. Listado de lotes para DataTable.
     * 2. Lotes por producto.
     * 2.1. Lotes producto y almacén.
     * 3. Crear lote.
     * 4. Guardar lote.
     * 5. Editar lote.
     * 6. Actualizar lote.
     * 7. Eliminar documento de lote.
     * 8. Eliminar lote.
     */
    
    private $option = 'lotes';  
    private $module = 'production';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:production');
        $this->middleware('batch_get', ['only' => ['edit', 'update', 'removeDoc', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación filtro:
        if(isset($data['submit_filter'])){
            return Validator::make($data, [
                'product_id' => 'nullable|integer'
            ]);    

        //Validación save:
        }elseif(isset($data['submit_save'])){
            return Validator::make($data, [
                'product_id' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'batch_num' => 'required'
            ]); 
        }        
    }

    /**
     * 1. Lotes.
     */
    public function index($product_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.lotes',
            'module' => $this->module,
            'slug' => 'batches'
        ]; 

        $product = $product_id? Product::find($product_id):false;

        $languages = $this->languages;

        return view('admin.batches.index')->with(compact('header', 'product', 'languages'));
    }

    /**
     * 1.1. Filtro de lotes.
     */
    public function getBatchesBy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/batches/list')
                ->withErrors($validator)
                ->withInput();
        }

        return redirect()->route('batches.index', [$request->product_id]);
    }

    /**
     * 1.2. Listado de lotes para DataTable.
     */
    public function getBatchesDT(Request $request, $product_id = false){
        $data = Batch::select('batches.id', 'batches.batch_num', 'batches.deadline', 'batches.expiry_date', 'batches.initial_qty', 'batches.doc_num', 'batches.doc', 'batches.created_at', 'products.name AS product', 'work_orders.id AS work_order_id', 'work_orders.work_order_num')
        ->join('products', 'batches.product_id', '=', 'products.id')
        ->leftJoin('work_orders', 'batches.work_order_id', '=', 'work_orders.id')
        ->when($product_id, function($q) use($product_id){
            return $q->where('batches.product_id', $product_id);
        })
        ->where('batches.batch_num', '!=', config('constants.NOBATCH_'))
        ->where('products.company_id', session('currentCompany'));

        return DataTables::of($data)
        ->addColumn('batch', function($row){
            return '<a target="_blank" href="/admin/batches/'.$row->id.'/edit">'.$row->batch_num.'</a>';
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
        ->rawColumns(['batch', 'work_order'])
        ->make(true);
    }

    /**
     * 2. Lotes por producto.
     */
    public function productBatches(Request $request, $product_id){
        if($request->ajax()){
            $data = Batch::select('id', 'batch_num')
            ->where('product_id', $product_id)
            ->groupBy('id')
            ->get();

            return response()->json($data);
        }
    }

    /**
     * 2.1. Lotes producto y almacén.
     */
    public function productBatchesByStore(Request $request, $product_id, $store_id){
        if($request->ajax()){
            $data = Stock::select('batches.id', 'batches.batch_num')
            ->join('batches', 'stocks.batch_id', '=', 'batches.id')
            ->where('stocks.product_id', $product_id)
            ->where('stocks.store_id', $store_id)
            ->where('stocks.result', '>', 0)
            ->orderBy('stocks.created_at', 'DESC')
            ->groupBy('stocks.batch_id')
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

        $patterns = BatchPatterns::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $languages = $this->languages;

        return view('admin.batches.create')->with(compact('header', 'patterns', 'languages')); 
    }

    /**
     * 4. Guardar lote.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/batches/create')
                ->withErrors($validator)
                ->withInput();
        }   

        //Numeración del lote:
        $batch_num = $request->batch_num? $request->batch_num:NULL;
        $num_cardinal = NULL;

        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('batch', $request->pattern_id);
            $batch_num = $patternNum['pattern_num'];
            $num_cardinal = $patternNum['num'];
        }

        //Documento:
        $fileName = NULL;
        if($request->hasFile('doc')){
            $file = $request->file('doc');

            //Extensión del archivo:
            $ext = helpers::mimeExtension($file->getClientMimeType());

            $path = public_path().'/docs/batches';
            $prefix = ($request->doc_num != '')? $request->doc_num:$file->getClientOriginalName();
            $fileName = $prefix.'-'.uniqid().$ext;
            $moved = $file->move($path, $fileName);

            //Si el archivo no ha sido guardado correctamente:
            if(!$moved){
                $error->doc = trans('textos.documento_error_guardar');
                return response()->json(['errors'=>$error]);   
            }
        }  

        //Formateando fechas:
        $deadline = $request->deadline != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->deadline):$request->deadline:NULL;
        $expiry_date = $request->expiry_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->expiry_date):$request->expiry_date:config('constants.UNDEFINED_DATE_');

        $b = new Batch();
        $b->product_id = $request->product_id;
        $b->batch_num = $batch_num;
        $b->num_cardinal = $num_cardinal;
        $b->pattern_id = $request->pattern_id;
        $b->deadline = $deadline;
        $b->expiry_date = $expiry_date;
        $b->initial_qty = $request->qty? $request->qty:'0';
        $b->doc_num = $request->doc_num;
        $b->doc = $fileName;
        $b->created_by = Auth::user()->id;
        $b->save();  

        //Generamos movimiento de stock si existe cantidad inicial y viene declarado el almacén:
        if($b->initial_qty > 0 && $request->store_id){
            //Obtenemos movimiento de entrada por fabricación:
            $mov = StockMovements::select('id', 'sign')
            ->where('acronym', 'ef')
            ->where('sign', 's')
            ->first();

            $data = [
                'product_id' => $b->product_id,
                'stock_mov_id' => $mov->id, 
                'batch_id' => $b->id,
                'store_id' => $request->store_id,
                'user_id' => Auth::user()->id,
                'qty' => $b->qty,
                'operation' => $mov->sign,
                'result' => $b->qty,
                'doc' => NULL,
                'doc_num' => NULL,
                'for_user' => NULL,
                'returned_at' => NULL,
                'product_serial_id' => NULL,
                'observations' => NULL,
                'store_destiny' => NULL    
            ];
            Stock::movStock($data);
        }

        $msg = trans('textos.lote_creado');
        return redirect()->route('batches.edit', $b->id)->with(compact('msg')); 
    }

    /**
     * 5. Editar lote.
     */
    public function edit(Batch $batch){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.lotes_editar',
            'module' => $this->module,
            'slug' => 'batches'
        ]; 

        $languages = $this->languages;

        $created_by = $batch->created_by? User::select('name', 'surname')->find($batch->created_by):false;
        $updated_by = $batch->updated_by? User::select('name', 'surname')->find($batch->updated_by):false;

        $product = Product::select('id', 'name')->find($batch->product_id);

        //Orden de trabajo:
        $work_order = $batch->work_order_id? WorkOrder::find($batch->work_order_id):false;

        return view('admin.batches.edit')->with(compact('header', 'batch', 'languages', 'created_by', 'updated_by', 'product', 'work_order'));    
    }

    /**
     * 6. Actualizar lote.
     */
    public function update(Request $request, Batch $batch){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/batches/'.$batch->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }  

        //Formateando fechas:
        $deadline = $request->deadline != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->deadline):$request->deadline:NULL;
        $expiry_date = $request->expiry_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->expiry_date):$request->expiry_date:config('constants.UNDEFINED_DATE_');

        //Documento:
        $fileName = false;
        if($request->hasFile('doc')){
            $file = $request->file('doc');

            //Extensión del archivo:
            $ext = helpers::mimeExtension($file->getClientMimeType());

            $path = public_path().'/docs/batches';
            $prefix = ($request->doc_num != '')? $request->doc_num:$file->getClientOriginalName();
            $fileName = $prefix.'-'.uniqid().$ext;
            $moved = $file->move($path, $fileName);

            //Si el archivo no ha sido guardado correctamente:
            if(!$moved){
                $error->doc = trans('textos.documento_error_guardar');
                return response()->json(['errors'=>$error]);   
            }
        }  

        if($request->batch_num){
            $batch->batch_num = $request->batch_num;
        }
        $batch->deadline = $deadline;
        if($expiry_date != config('constants.UNDEFINED_DATE_')){
            $batch->expiry_date = $expiry_date; 
        }
        $batch->doc_num = $request->doc_num;
        if($fileName){
            $batch->doc = $fileName;
        }
        $batch->updated_by = Auth::user()->id;
        $batch->save();

        $msg = trans('textos.lote_updated');
        return redirect()->route('batches.edit', [$batch->id, false])->with(compact('msg'));
    }

    /**
     * 7. Eliminar documento de lote.
     */
    public function removeDoc(Batch $batch){
        File::delete(public_path().'/docs/batches/'.$batch->doc);

        $batch->doc = NULL;
        $batch->save();

        $msg = trans('textos.archivo_deleted');
        return redirect()->route('batches.edit', $batch)->with(compact('msg'));
    }

    /**
     * 8. Eliminar lote.
     */
    public function destroy(Request $request, Batch $batch){
        if($batch->doc){
            File::delete(public_path().'/docs/batches/'.$batch->doc);
        }

        $batch->delete();

        $msg = trans('textos.lote_deleted');
        return redirect()->route('batches.index')->with(compact('msg'));
    }
}
