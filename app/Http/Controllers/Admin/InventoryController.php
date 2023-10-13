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
use App\Models\Product;
use App\Models\Stock;

class InventoryController extends Controller{
    /**
     * 1. Inventario.
     * 1.1. Listado de inventario para DataTable.
     */
    
    private $option = 'stocks';
    private $module = 'stocks';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
    }

    /**
     * 1. Inventario.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.inventario',
            'module' => $this->module,
            'slug' => 'inventory'
        ];   

        return view('admin.inventory.index')->with(compact('header')); 
    }

    /**
     * 1.1. Listado de inventario para DataTable.
     */
    public function getInventoryDT(){
        $data = Product::select('id', 'name', 'ref', 'manual_ref', 'batch', 'status', 'created_at',
            DB::raw('(SELECT doc FROM product_docs WHERE product_id = products.id AND featured = 1 LIMIT 1) AS Image'),
        )
        ->where('company_id', session('currentCompany'))
        ->where('stock_management', 1)
        ->orderBy('name', 'ASC');

        return DataTables::of($data)
        ->addColumn('product', function($row){
            return $row->name;
        })
        ->addColumn('ref', function($row){
            $ref = $row->ref? $row->ref:($row->manual_ref? $row->manual_ref:'');
            return $ref;
        })
        ->addColumn('alta', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="/img/products/'.$row->Image.'" width="50"><span style="display: none">1</span>';  
            }else{
                return '<i class="fa fa-gift icono"></i><span style="display: none">0</span>';
            }
        })
        ->addColumn('estado', function($row){

        })
        ->addColumn('acciones', function($row){
            return view('admin.inventory.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['image'])
        ->make(true);
    }
}
