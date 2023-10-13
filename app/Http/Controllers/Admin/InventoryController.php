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
        ->addColumn('status', function($row){
            if($row->status == 1){
                return '<i class="fa fa-check text-success" rel="tooltip" title="'.ucfirst(trans('textos.activo')).'"></i>';
            }else{
                return '<i class="fa fa-ban text-danger" rel="tooltip" title="'.ucfirst(trans('textos.inactivo')).'"></i>';
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.inventory.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['image', 'status'])
        ->make(true);
    }

    /**
     * 2. Inventario por producto.
     */
    public function getInventoryByProduct($product_id){
        $batches = Batch::select('id', 'batch_num')
        ->where('product_id', $product_id)
        ->get();

        $data = [];

        foreach($batches as $b){
            $stock = Stock::select('result')
            ->where('batch_id', $b->id)
            ->orderBy('id', 'DESC')
            ->first();

            if($stock && $stock > 0){
                $arr = [$b->batch_num, $stock->result];
                array_push($data, $arr);
            }
        }

        return response()->json($data); 
    }
}
