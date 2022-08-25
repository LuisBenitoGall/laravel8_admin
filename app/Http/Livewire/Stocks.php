<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Batch;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductSerials;
use App\Models\Stock;
use App\Models\StockMovements;
use App\Models\Store;

class Stocks extends Component{
    /**
     * 1. Render componente.
     * 2. Seleccionar producto.
     * 3. Guardar stock.
     * 4. Select 2.
     */

    public $currency;
    public $doc;
    public $doc_num;
    public $employees;
    public $languages;
    public $last_movements;
    public $movement_selected;
    public $movements;
    public $observations;
    public $product;
    public $products;
    public $qty;
    public $stock_by_store = [];
    public $store_selected;
    public $stores;

    protected $listeners = [
        'productSelected',
        'store_selected'
    ];

    protected $rules = [
        'product' => 'required',
        'qty' => 'required',
        'store_selected' => 'required|integer',
        'movement_selected' => 'required|integer'
    ];

    public function mount(){
        //Productos, sólo productos activos con gestión de stock:
        $this->products = Product::select('id', 'name', 'ref', 'manual_ref', 'translations', 'price', 'iva', 'batch', 'on_sale', 'created_at')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->where('type', 'p')
        ->where('stock_management', 1)
        ->orderBy('name', 'ASC')
        ->get();

        //Moneda:
        $this->currency = CompanySettings::co_currency(0);

        //Idiomas:
        $this->languages = helpers::languages();

        //Almacenes:
        $this->stores = Store::select('id', 'name', 'type')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();

        //Movimientos de stock:
        $this->movements = StockMovements::select('id', 'name', 'acronym', 'sign', 'basic', 'domestic_consumption', 'limit')
        ->where('basic', 1)
        ->orWhere('company_id', session('currentCompany'))
        ->get();

        //Empleados:
        $this->employees = Employee::companyEmployees(session('currentCompany'));

        $this->last_movements = false;
        $this->movement_selected = false;
        $this->store_selected = false;
        $this->product = false;
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        //Histórico de movimientos:
        $this->last_movements = Stock::select('stocks.created_at', 'products.id', 'products.name')
        ->join('products', 'stocks.product_id', '=', 'products.id')
        ->where('products.company_id', session('currentCompany'))
        ->orderBy('stocks.created_at', 'DESC')
        ->take(10)
        ->get();

        return view('livewire.stocks');
    }

    /**
     * 2. Seleccionar producto.
     */
    public function productSelected($product_id){
        $this->product = Product::find($product_id);

        if(!$this->product) $this->store_selected = false;

        //Stocks del producto por almacén:
        foreach($this->stores as $str){
            $stock = Stock::select('result')
            ->where('product_id', $product_id)
            ->where('store_id', $str->id)
            ->orderBy('created_at', 'DESC')
            ->first();

            $this->stock_by_store[$str->id] = $stock? $stock->result:'0';
        }

        // $this->stock_by_store = Stock::select(DB::raw('() AS total'))
        // ->where('product_id', $id)
        // ->toArray('result', 'store_id');


        //dd($this->product);
    }

    /**
     * 3. Seleccionar almacén
     */
    public function storeSelected($store_id){
        $this->store_selected = $store_id;
    }

    /**
     * 4. Guardar stock.
     */
    public function store(){
        $this->validate();

        //Validando almacén:
        $company = Store::select('company_id')->find($this->store_selected);
        if($company->company_id != session('currentCompany')){
            return false;
        }

        //Tipo de operación:
        $operation = false;
        foreach($this->movements as $mov){
            if($mov->id == $this->movement_selected){
                $operation = $mov;
            }
        }


        dd($operation);
        
        $result = 1;

        $row = new Stock();
        $row->product_id = $this->product->id;
        $row->stock_mov_id = $this->movement_selected;
        $row->batch_id = NULL;
        $row->store_id = $this->store_selected;
        $row->user_id = Auth::user()->id;
        $row->qty = $this->qty;
        $row->operation = NULL;
        $row->result = $result;
        $row->doc = $this->doc;
        $row->doc_num = $this->doc_num;
        $row->for_user = NULL;
        $row->accepted_at = NULL;
        $row->returned_at = NULL;
        $row->product_serial_id = NULL;
        $row->observations = $this->observations;
        $row->save();  
    }

    /**
     * 4. Select 2.
     */
    public function hydrate(){
        $this->emit('select2_lw');
    }
}
