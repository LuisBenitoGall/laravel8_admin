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
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerials;
use App\Models\StockMovements;
use App\Models\Stock;
use App\Models\Store;

class StockController extends Controller{
    /**
     * 1. Stock de productos.
     */

    private $option = 'stocks';
    private $module = 'stocks';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:stocks');
        $this->middleware('module_setted:products');
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
        $products = Product::companyProducts(session('currentCompany'), session('currentCompany'));

        //Almacenes:
        $stores = Store::select('id', 'name', 'type')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();

        //Movimientos de stock:
        $movements = StockMovements::where('basic', 1)
        ->orWhere('company_id', session('currentCompany'))
        ->get();

        //Employees:
        $employees = Employee::companyEmployees(session('currentCompany'));

        return view('admin.stocks.index')->with(compact('header', 'storeSelected', 'stockMovId', 'productSelected', 'stockResultante', 'products', 'stores', 'movements', 'employees'));
    }
}
