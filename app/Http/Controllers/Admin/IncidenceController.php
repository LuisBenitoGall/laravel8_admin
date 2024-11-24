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
use Carbon\Carbon;
use File;

//Models:
use App\Models\CompanySettings;
use App\Models\Incidence;

class IncidenceController extends Controller{
    /**
     * 1. Listado de incidencias.
     * 2. Formulario nueva incidencia.
     * 3. Guardar incidencia.
     */
    
    private $option = 'incidencias';
    private $module = 'quality';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:quality');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])) {
            return Validator::make($data, [
                'incidence_date' => 'required',
                'concept' => 'required',
                'customer_id' => 'required_without:subject_id',
                'subject_id' => 'required_without:customer_id',
            ],[
                'customer_id.required_without' => trans('textos.empresa_usuario_requerido'), 
                'subject_id.required_without' => trans('textos.empresa_usuario_requerido')
            ]);
        }
    }

    /**
     * 1. Listado de incidencias.
     */
    public function index(){
         $header = [
            'option' => $this->option, 
            'suboption' => 'textos.incidencias',
            'module' => $this->module,
            'slug' => 'incidences'
        ]; 

        return view('admin.incidences.index')->with(compact('header'));
    }

    /**
     * 2. Formulario nueva incidencia.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.incidencia_nueva',
            'module' => $this->module,
            'slug' => 'incidences'
        ]; 

        $languages = $this->languages;

        return view('admin.incidences.create')->with(compact('header', 'languages'));     
    }

    /**
     * 3. Guardar incidencia.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/incidences/create')
                ->withErrors($validator)
                ->withInput();
        }   

        $incidence_date = $request->incidence_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->incidence_date):$request->incidence_date:NULL;

        $incidence = new Incidence();
        $incidence->incidence_date = $incidence_date;
        $incidence->origin = $request->origin;
        $incidence->provider_id = session('currentCompany');
        $incidence->subject_id = $request->subject_id;
        $incidence->customer_id = $request->customer_id;
        $incidence->concept = $request->concept;
        $incidence->observations = $request->observations;
        $incidence->created_by = Auth::user()->id;
        $incidence->save();

        $msg = trans('textos.incidencia_creada');
        return redirect()->route('incidences.edit', $incidence)->with(compact('msg'));
    }

    /**
     * 4. Editar incidencia.
     */
    public function edit(Incidence $incidence, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.incidencia_editar',
            'module' => $this->module,
            'slug' => 'incidences'
        ]; 

        $languages = $this->languages;

        return view('admin.incidences.edit')->with(compact('header', 'incidence', 'tab', 'languages')); 
    }

    /**
     * 5. Actualizar incidencia.
     */
    public function update(Request $request){

    }
}
