<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use App\Notifications\PurchaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;
use PDF;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\Order;
use App\Models\Sat;
use App\Models\SatItem;
use App\Models\SatPatterns;

class SatController extends Controller{
    /**
     * 1. Servicios por empresa.
     * 1.1. Lista de servicios por empresa.
     * 2. Formulario nuevo servicio.
     * 3. Guardar nuevo servicio.
     * 4. Editar servicio.
     * 5. Actualizar servicio.
     */
    
    private $option = 'servicios_asistencia_tecnica';
    private $module = 'sat';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:sat');
    }

    /**
     * 1. Servicios por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.servicios',
            'module' => $this->module,
            'slug' => 'sat'
        ];  

        return view('admin.sats.index')->with(compact('header'));   
    }

    /**
     * 1.1. Lista de servicios por empresa.
     */
    public function getSatsDT(){
        $data = Sat::where('company_id', session('currentCompany'));

        return DataTables::of($data)

        ->make(true);
    }

    /**
     * 2. Formulario nuevo servicio.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.sat_nuevo',
            'module' => $this->module,
            'slug' => 'sat'
        ];  

        return view('admin.sats.create')->with(compact('header'));     
    }

    /**
     * 3. Guardar nuevo servicio.
     */
    public function store(Request $request){
        dd($request->all());

        $sat = new Sat();
        $sat->company_id = session('currentCompany');
        $sat->contractor_id = $request->company_id;
        $sat->user_id = Auth::user()->id;
        $sat->save();

        //Generamos pedido de venta:


        return redirect()->route('sat.edit', $sat);
    }

    /**
     * 4. Editar servicio.
     */
    public function edit(Sat $sat){

    }

    /**
     * 5. Actualizar servicio.
     */
    public function update(Request $request){

    }
    
}
