<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use File;

//Models:
use App\Models\Batch;
use App\Models\BatchPatterns;
use App\Models\CompanySettings;
use App\Models\Delivery;
use App\Models\DeliveryItems;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerials;
use App\Models\Stock;
use App\Models\StockMovements;
use App\Models\Store;

class StockController extends Controller{
    /**
     * 1. Stock de productos.
     * 1.1. Stock por almacén para DataTables.
     * 2. Histórico de movimientos de producto.
     * 2.1. Movimientos por producto para DataTable.
     * 3. Guardar movimiento de stock.
     */

    private $option = 'stocks';
    private $module = 'stocks';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:stocks');
        $this->middleware('module_setted:products');
        $this->middleware('product_get', ['only' => ['show']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'product_id' => 'required|integer',
                'store_id' => 'required|integer|different:to_store',
                'movement' => 'required|integer',
                'qty' => 'required|numeric',
            ],[
                'movement.required' => trans('textos.movimiento_selec_aviso'),
                'store_id.different' => trans('textos.almacenes_diferentes_aviso')    
            ]);
        }
    }

    /**
     * 1. Stock de productos.
     */
    public function index($product_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.stock_productos',
            'module' => $this->module,
            'slug' => 'stocks'
        ];

        $stockMovId = false;
        $productSelected = false;
        $stockResultante = false;

        //Productos, sólo productos con gestión de stock:
        $products = Product::select('id', 'name', 'ref', 'manual_ref', 'translations', 'price', 'iva', 'batch', 'on_sale', 'created_at')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->where('type', 'p')
        ->where('stock_management', 1)
        ->orderBy('name', 'ASC')
        ->get();

        //Almacenes, que pasamos a una session:
        $stores = Store::select('id', 'name', 'type')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();

        session(['currentCompanyStores' => $stores]);

        //Movimientos de stock:
        $movements = StockMovements::orderBy('name', 'ASC')
        ->get();

        $movements_arr = [];
        foreach($movements as $m){
            $movements_arr[$m->id] = $m->name;

            //Modificamos la explicación:
            $explanation = '';
            if($m->explanation){
                $p = unserialize($m->explanation);
                $explanation = $p[session()->get('locale')];
            }
            $m->explanation_ = $explanation;
        }

        //Últimos movimientos de stock realizados.
        $last_movements = Stock::select('stocks.qty', 'stocks.operation', 'stocks.created_at', 'products.id', 'products.name')
        ->join('products', 'stocks.product_id', '=', 'products.id')
        ->where('products.company_id', session('currentCompany'))
        ->orderBy('stocks.created_at', 'DESC')
        ->orderBy('stocks.id', 'DESC')
        ->take(10)
        ->get();

        //Patrones para lotes:
        $batch_patterns = BatchPatterns::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Employees:
        $employees = Employee::companyEmployees(session('currentCompany'));

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Employees:
        $employees_data = Employee::companyEmployees(session('currentCompany'));
        $employees = [];
        foreach($employees_data as $e){
            $employees[$e->id] = $e->name.' '.$e->surname;
        }

        $languages = $this->languages;

        return view('admin.stocks.index')->with(compact('header', 'product_id', 'stockMovId', 'productSelected', 'stockResultante', 'products', 'stores', 'movements', 'movements_arr', 'last_movements', 'batch_patterns', 'employees', 'currency', 'languages'));
    }

    /**
     * 1.1. Stock por almacén para DataTables.
     */
    public function storesStock(Request $request, $product_id){
        if($request->ajax()){
            $data = [];

            //Lotes del producto:
            $batches = Batch::select('id', 'batch_num')->where('product_id', $product_id)->get();
            //06/08/2023: todo producto debe tener al menos 1 lote, incluso los productos sin gestión de lotes:
            if(!$batches->count()){
                $b_ = new Batch();
                $b_->product_id = $product_id;
                $b_->batch_num = config('constants.NOBATCH_');
                $b_->expiry_date = config('constants.UNDEFINED_DATE_');
                $b_->created_by = Auth::user()->id;
                $b_->save();

                $batches = Batch::select('id', 'batch_num')->where('product_id', $product_id)->get();
            }

            //Almacenes de la empresa, que obtenemos de los guardados en session:
            if(session('currentCompanyStores') && !empty(session('currentCompanyStores'))){
                $stores = session('currentCompanyStores');
            }else{
                $stores = Store::select('id', 'name', 'type')
                ->where('company_id', session('currentCompany'))
                ->where('status', 1)
                ->get();

                session(['currentCompanyStores' => $stores]);
            }

            foreach($stores as $s){
                if($batches->count()){
                    //Variable para controlar que el almacén se muestre al menos 1 vez en caso de no tener ninguna cantidad:
                    $store_in = false;

                    foreach($batches as $b){
                        $stock = Stock::select('id', 'result')
                        ->where('product_id', $product_id)
                        ->where('store_id', $s->id)
                        ->where('batch_id', $b->id)
                        ->orderBy('id', 'DESC')
                        ->orderBy('result', 'DESC')
                        ->first();

                        $batch = $stock && $stock->result > 0? $b->batch_num:'';

                        //Si el almacén no aparece:
                        if(!$store_in){
                            $row = [
                                'store' => $s->name,
                                'store_id' => $s->id,
                                'batch' => $batch,
                                'batch_id' => $b->id,
                                'stock' => $stock? $stock->result:'0',
                                'stock_id' => $stock? $stock->id:''
                            ];
                            array_push($data, $row);

                            $store_in = true;

                        //Si el almacén aparece y la cantidad en stock es superior a 0:
                        }elseif($store_in && $stock && $stock->result > 0){
                            $row = [
                                'store' => $s->name,
                                'store_id' => $s->id,
                                'batch' => $batch,
                                'batch_id' => $b->id,
                                'stock' => $stock? $stock->result:'0',
                                'stock_id' => $stock? $stock->id:''
                            ];
                            array_push($data, $row);    
                        }
                    }

                //Producto sin lotes:
                }else{
                    $row = [
                        'store' => $s->name,
                        'store_id' => $s->id,
                        'batch' => '',
                        'batch_id' => '',
                        'stock' => 0,
                        'stock_id' => ''
                    ];
                    array_push($data, $row);  
                }
            }

            return response()->json($data);
        }
    }

    /**
     * 2. Histórico de movimientos de producto.
     */
    public function show(Product $product){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.historico_movimientos',
            'module' => $this->module,
            'slug' => 'stocks'
        ];   

        return view('admin.stocks.show')->with(compact('header', 'product'));
    }

    /**
     * 2.1. Movimientos por producto para DataTable.
     */
    public function getProductStockMovementsDT($product_id){
        $data = Stock::select('stocks.qty', 'stocks.result', 'stocks.operation', 'stocks.doc', 'stocks.doc_num', 'stocks.observations', 'stocks.created_at', 'products.id', 'products.name', 'stock_movements.name AS movement', 'stores.name AS store', 'batches.batch_num AS batch', 'users.name AS Nombre', 'users.surname')
        ->join('products', 'stocks.product_id', '=', 'products.id')
        ->join('stock_movements', 'stocks.stock_mov_id', '=', 'stock_movements.id')
        ->join('stores', 'stocks.store_id', '=', 'stores.id')
        ->leftJoin('users', 'stocks.user_id', '=', 'users.id')
        ->leftJoin('batches', 'stocks.batch_id', '=', 'batches.id')
        ->where('stocks.product_id', $product_id)
        ->orderBy('stocks.created_at', 'DESC');

        return DataTables::of($data)
        ->addColumn('fecha', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('movement', function($row){
            return ucfirst($row->movement);
        })
        ->addColumn('store', function($row){
            return $row->store;
        })
        ->addColumn('batch', function($row){
            if($row->batch != '' && $row->batch != config('constants.NOBATCH_')){
                return $row->batch;
            }else{
                return '';
            }
        })
        ->addColumn('qty', function($row){
            if($row->operation == 'r'){
                return '-'.$row->qty;
            }else{
                return '+'.$row->qty;    
            }
        })
        ->addColumn('stock', function($row){
            return $row->result;
        })
        ->addColumn('doc', function($row){
            $doc = '';
            if($row->doc){
                $doc .= '<a target="_blank" href="/docs/stock-docs/'.$row->doc.'">';
                    if($row->doc_num){
                        $doc .= $row->doc_num;
                    }else{
                        $doc .= '<i class="fa fa-file"></i>';
                    }
                $doc .= '</a>';
            }
            return $doc;
        })
        ->addColumn('user', function($row){
            if($row->Nombre){
                return $row->Nombre.' '.$row->surname;
            }else{
                return '';
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.stocks.partials.dt-actions', compact('row'));
        })
        ->rawColumns(['doc'])
        ->make(true);
    }

    /**
     * 3. Guardar movimiento de stock.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/stocks/'.$request->product_id)
                ->withErrors($validator)
                ->withInput();
        }

        //Producto:
        $product = Product::select('id', 'price', 'batch', 'stock_management')->find($request->product_id);
        // ¿Tiene gestión de stock?
        if(!$product->stock_management){
            $alert = trans('textos.producto_sin_gestion_stock');
            return redirect()->route('stocks.index', $request->product_id)->with(compact('alert'));   
            exit; 
        }

        //Fecha de caducidad:
        $expiry_date = $request->expiry_date? (session()->get('locale') != 'en')? helpers::convertFecha($request->expiry_date):$request->expiry_date:config('constants.UNDEFINED_DATE_'); 

        //Lote:
        //1. Producto sin lotes: siempre toma lote único.
        if(!$product->batch){
            $batch = Batch::firstOrCreate(
                ['product_id' => $product->id],
                ['product_id' => $product->id, 'batch_num' => config('constants.NOBATCH_'), 'expiry_date' => config('constants.UNDEFINED_DATE_'), 'created_by' => Auth::user()->id]
            );

        //2. Producto con gestión de lotes:
        }else{
            //Viene declarado el lote:
            if($request->batch_id){
                $batch = Batch::find($request->batch_id);     
            
            //No viene declarado el lote:
            }else{
                $batch_num = NULL;
                $num_cardinal = NULL;
                $pattern_id = NULL;

                //Número de lote manual:
                if($request->new_batch){
                    $batch_num = $request->new_batch;

                //Número por patrón:
                }elseif($request->pattern_id){
                    $patternNum = $this->setPatternNumber('batch', $request->pattern_id);

                    $batch_num = $patternNum['pattern_num'];
                    $num_cardinal = $patternNum['num'];
                    $pattern_id = $request->pattern_id;
                }

                $data_batch = [
                    'product_id' => $product->id,
                    'work_order_id' => NULL,
                    'batch_num' => $batch_num,
                    'num_cardinal' => $num_cardinal,
                    'pattern_id' => $pattern_id,
                    'deadline' => NULL,
                    'expiry_date' => $expiry_date,
                    'initial_qty' => 0,
                    'doc_num' => NULL,
                    'doc' => NULL
                ];
                $batch = Batch::saveBatch($data_batch);
            }
        }

        //Tipo de movimiento:
        $stock_mov = StockMovements::select('acronym', 'sign')->find($request->movement);
        if(!$stock_mov){
            $alert = trans('textos.movimiento_no_existe');
            return redirect()->route('stocks.index', $request->product_id)->with(compact('alert'));   
            exit; 
        }

        //Recuperamos última línea del producto, almacén y lote:
        $result = 0;
        if($request->stock_id){
            //request no trae lote en el traspaso entre almacenes
            $last_row = Stock::where('batch_id', $batch->id)
            ->where('product_id', $product->id)
            ->where('store_id', $request->store_id)
            ->orderBy('id', 'DESC')
            ->first();
            $result = $last_row? $last_row->result:0;
        }
        //dd($request->all(), $result, $batch, $request->new_batch, $expiry_date);
        //Actualizando result:
        if($stock_mov->sign == 's'){
            $result = $result + $request->qty;  
            $operation = 's'; 

        }elseif($stock_mov->sign == 'r' || $stock_mov->sign == 'x'){
            $result = $result - $request->qty;

            //NO se pueden dar resultados negativos:
            if($result < 0){
                $alert = trans('textos.stock_negativo_warning');
                return redirect()->route('stocks.index', $request->product_id)->with(compact('alert'));
                exit;
            }

            $operation = 'r';
        }

        //Documento:
        $fileName = NULL;
        if($request->hasFile('doc')){
            $file = $request->file('doc');

            //Extensión del archivo:
            $ext = helpers::mimeExtension($file->getClientMimeType());

            $path = public_path().'/docs/stock-docs';
            $prefix = ($request->doc_num && $request->doc_num != '')? $request->doc_num:$file->getClientOriginalName();
            $fileName = $prefix.'-'.uniqid().$ext;
            $moved = $file->move($path, $fileName);

            //Si el archivo no ha sido guardado correctamente:
            if(!$moved){
                $error->doc = trans('textos.documento_error_guardar');
                return response()->json(['errors'=>$error]);   
            }
        }  

        $stock = new Stock();
        $stock->product_id = $product->id;
        $stock->stock_mov_id = $request->movement;
        $stock->order_id = $request->order? $request->order:NULL;
        $stock->batch_id = $batch->id;
        $stock->store_id = $request->store_id;
        $stock->user_id = Auth::user()->id;
        $stock->qty = $request->qty;
        $stock->operation = $operation;
        $stock->result = $result;
        $stock->doc = $fileName;
        $stock->doc_num = $request->doc_num;
        $stock->for_user = $request->for_user;
        $stock->observations = $request->observations;
        $stock->save();

        //Destino de movimientos de traspaso:
        if($stock_mov->sign == 'x' && $request->to_store){
            //Stock en el almacén de destino:
            $last_result = Stock::select('result')
            ->where('product_id', $stock->product_id)
            ->where('batch_id', $stock->batch_id)
            ->where('store_id', $request->to_store)
            ->orderBy('id', 'DESC')
            ->first();

            $result2 = $last_result? $last_result->result + $request->qty:$request->qty;

            $stock2 = new Stock();
            $stock2->product_id = $product->id;
            $stock2->stock_mov_id = $request->movement;
            $stock2->batch_id = $batch->id;
            $stock2->store_id = $request->to_store;         //Almacén de destino.
            $stock2->user_id = Auth::user()->id;
            $stock2->qty = $request->qty;
            $stock2->operation = 's';
            $stock2->result = $result2;
            $stock2->doc = $fileName;
            $stock2->doc_num = $request->doc_num;
            $stock2->for_user = $request->for_user;
            $stock2->observations = $request->observations;
            $stock2->save();
        }

        //Acciones adicionales en función del tipo de movimiento:
        //1. Si existe pedido:
        if($request->order){
            //Gestión de albaranes:
            $deliveries = Delivery::join('delivery_orders', 'deliveries.id', '=', 'delivery_orders.delivery_id')
            ->where('delivery_orders.order_id', $request->order)
            ->pluck('deliveries.id')
            ->toArray();

            //Líneas de albarán del producto:
            $delivery_items = DeliveryItems::whereIn('delivery_id', $deliveries)
            ->get();

            //11/08/2023: queda por revisar esta lógica.
        }        

        $msg = trans('textos.movimiento_ok');
        return redirect()->route('stocks.index', $request->product_id)->with(compact('msg'));
    }
   
}
