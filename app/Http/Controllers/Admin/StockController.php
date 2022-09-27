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
     * 2. Histórico de movimientos de producto.
     * 3. Select 2.
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

    /**
     * 1. Stock de productos.
     */
    public function index($storeSelected = false){
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

        //Almacenes:
        $stores = Store::select('id', 'name', 'type')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();

        //Movimientos de stock:
        $movements = StockMovements::orderBy('name', 'ASC')
        ->get();

        //Últimos movimientos de stock realizados.
        $last_movements = Stock::select('stocks.created_at', 'products.id', 'products.name')
        ->join('products', 'stocks.product_id', '=', 'products.id')
        ->where('products.company_id', session('currentCompany'))
        ->orderBy('stocks.created_at', 'DESC')
        ->take(10)
        ->get();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Employees:
        $employees = Employee::companyEmployees(session('currentCompany'));

        return view('admin.stocks.index')->with(compact('header', 'storeSelected', 'stockMovId', 'productSelected', 'stockResultante', 'products', 'stores', 'movements', 'last_movements', 'currency', 'employees'));
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

   
}
