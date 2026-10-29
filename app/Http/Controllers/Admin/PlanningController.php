<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Planning;

class PlanningController extends Controller{
    /**
     * 1. Planificación.
     * 2. Formulario nueva planificación.
     */
    
    private $option = 'planning';
    private $module = 'production';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:production');
        //$this->middleware('work_order_pattern_get', ['only' => ['edit', 'update', 'destroy']]);
    }    

    /**
     * 1. Planificación.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.planificacion_mi',
            'module' => $this->module,
            'slug' => 'planning'
        ];    

        return view('admin.planning.index')->with(compact('header'));
    }

    /**
     * 2. Formulario nueva planificación.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.planificacion_nueva',
            'module' => $this->module,
            'slug' => 'planning'
        ];   

        //Estados de producción:
        $production_status = helpers::productionProductStatus(); 

        return view('admin.planning.create')->with(compact('header', 'production_status'));    
    }
}
