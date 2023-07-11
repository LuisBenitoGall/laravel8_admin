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
use App\Models\Product;
use App\Models\WorkOrder;
use App\Models\WorkOrderPhase;

class WorkOrderPhaseController extends Controller{
    /**
     * 1. Fases por producto.
     * 1.1. Listado de fases para DataTable.
     */
    
    private $option = 'produccion';
    private $module = 'production';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:production');
    }   

    /**
     * 1. Fases por producto.
     */
    public function index($product_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.fases',
            'module' => $this->module,
            'slug' => 'phases'
        ];   

        $product = $product_id? Product::find($product_id):false; 

        return view('admin.work_order_phases.index')->with(compact('header', 'product'));    
    }

    /**
     * 1.1. Listado de fases para DataTable.
     */
    public function getPhasesDT($product_id){
        //$data = WorkOrderPhase::
    }
}
