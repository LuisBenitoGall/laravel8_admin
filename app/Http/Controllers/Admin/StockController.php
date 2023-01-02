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
use App\Models\CompanySettings;
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
     * 1.1. Stock por almacén para table.
     * 2. Histórico de movimientos de producto.
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
                'store_id' => 'required|integer',
                'movement' => 'required|integer',
                'qty' => 'required|numeric'
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
        }

        //Últimos movimientos de stock realizados.
        $last_movements = Stock::select('stocks.created_at', 'products.id', 'products.name')
        ->join('products', 'stocks.product_id', '=', 'products.id')
        ->where('products.company_id', session('currentCompany'))
        ->orderBy('stocks.created_at', 'DESC')
        ->take(10)
        ->get();

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

        return view('admin.stocks.index')->with(compact('header', 'product_id', 'stockMovId', 'productSelected', 'stockResultante', 'products', 'stores', 'movements', 'movements_arr', 'last_movements', 'employees', 'currency', 'employees', 'languages'));
    }

    /**
     * 1.1. Stock por almacén para DataTables.
     */
    public function storesStock(Request $request, $product_id){
        if($request->ajax()){
            $data = [];

            //Lotes del producto:
            $batches = Batch::select('id', 'name')->where('product_id', $product_id)->get();

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
                foreach($batches as $b){
                    $stock = Stock::select('id', 'result')
                    ->where('product_id', $product_id)
                    ->where('store_id', $s->id)
                    ->where('batch_id', $b->id)
                    ->orderBy('id', 'DESC')
                    ->first();

                    $row = [
                        'store' => $s->name,
                        'store_id' => $s->id,
                        'batch' => $b->name? $b->name:'',
                        'batch_id' => $b->id,
                        'stock' => $stock? $stock->result:'0',
                        'stock_id' => $stock? $stock->id:''
                    ];
                    array_push($data, $row);
                }
            }

            return response()->json($data);

            // $product->stocks()->get();
            // $stock = collect($product->stocks()->with('stores')->get());
            // $result = collect($stock)->sortBy('created_at',SORT_DESC,true)->unique('store_id');

            // return response()->json($result->all());

            // $data = Store::select('stocks.id', 'stocks.qty', 'stocks.result', 'stores.id AS storeId', 'stores.name')
            // ->leftJoin('stocks', 'stores.id', '=', 'stocks.store_id')
            // ->where('stocks.product_id', $product_id)
            // ->groupBy('stores.id')
            // ->get();

            // //dd($data);

            // return DataTables::of($data)
            // ->addColumn('name', function($row){
            //     return $row->name; 
            // })
            // ->addColumn('batch', function($row){
            //     return ''; 
            // })
            // ->addColumn('stock', function($row){
            //     return $row->result; 
            // })
            // ->addColumn('acciones', function($row){
            //     return '';   
            // })
            // ->make(true);
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
     * 3. Guardar movimiento de stock.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/stocks')
                ->withErrors($validator)
                ->withInput();
        }

        //Tipo de movimiento:
        $stock_mov = StockMovements::select('sign')->find($request->movement);

        //Recuperamos última línea del producto, almacén y lote:
        $result = 0;
        if($request->stock_id){
            $last_row = Stock::find($request->stock_id);   
            $result = $last_row? $last_row->result:0;
        }

        //Actualizando result:
        if($stock_mov->sign == 's'){
            $result = $result + $request->qty;    
        }elseif($stock_mov->sign == 'r' || $stock_mov->sign == 'x'){
            $result = $result - $request->qty;
        }

        //Lote:
        $batch_id = $request->batch? $request->batch:NULL;
        if(!$batch && $request->new_batch){
            $expiry_date = $request->expiry_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->expiry_date):$request->expiry_date:NULL;

            $batch = new Batch();
            $batch->product_id = $request->product_id;
            $batch->name = $request->batch;
            $batch->expiry_date = $expiry_date;
            $batch->initial_qty = $request->qty;
            $batch->save();

            $batch_id = $batch->id;
        }

        $stock = new Stock();
        $stock->product_id = $request->product_id;
        $stock->stock_mov_id = $request->movement;
        $stock->batch_id = $batch_id;
        $stock->store_id = $request->store_id;
        $stock->user_id = Auth::user()->id;
        $stock->qty = $request->qty;
        $stock->operation = $stock_mov->sign;
        $stock->result = $result;
        $stock->doc = $request->doc;
        $stock->doc_num = $request->doc_num;
        $stock->for_user = $request->for_user;
        $stock->observations = $request->observations;
        $stock->save();

        //Destino de movimientos de traspaso:
        if($stock_mov->sign == 'x'){

        }

        $msg = trans('textos.movimiento_ok');
        return redirect()->route('stocks.index', $request->product_id)->with(compact('msg'));
    }
   
}
