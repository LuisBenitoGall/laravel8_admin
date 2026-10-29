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
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Incidence;
use App\Models\IncidencePatterns;

class IncidenceController extends Controller{
    /**
     * 1. Listado de incidencias.
     * 1.1. Filtro de incidencias.
     * 1.2. Listado de incidencias para DataTable.
     * 2. Formulario nueva incidencia.
     * 3. Guardar incidencia.
     * 4. Editar incidencia.
     * 5. Actualizar incidencia.
     * 6. Eliminar incidencia.
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

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'incidence_date' => 'required',
                'concept' => 'required'
            ],[

            ]);
        }
    }

    /**
     * 1. Listado de incidencias.
     */
    public function index(Request $request){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.incidencias',
            'module' => $this->module,
            'slug' => 'incidences'
        ]; 

        $incidence_status = helpers::incidenceStatus();

        $languages = $this->languages;

        return view('admin.incidences.index')->with(compact('header', 'incidence_status', 'languages'));
    }

    /**
     * 1.1. Filtro de incidencias.
     */
    public function getIncidencesBy(Request $request){
        //Si viene declarado el número de incidencia redirigimos:
        if($request->incidence_id){
            $incidenceNum = $request->incidence_id;

            $incidence = Incidence::select('id')
            ->where('provider_id', session('currentCompany'))
            ->where(function($q) use ($incidenceNum){
                $q->where('id', $incidenceNum)
                ->orWhere('incidence_num', $incidenceNum)
                ->orWhere('incidence_old_num', $incidenceNum);
            })
            ->first();

            if(!$incidence){
                $alert = trans('textos.incidencia_no_existe');
                return redirect()->route('incidences.index')->with(compact('alert'));   
                exit;
            }else{
                return redirect()->route('incidences.edit', $incidence->id);    
                exit;   
            }
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.incidencias',
            'module' => $this->module,
            'slug' => 'incidences'
        ]; 

        $incidence_status = helpers::incidenceStatus();

        $customer = $request->customer_id? Company::select('id', 'name', 'tradename')->find($request->customer_id):false;
        $from = $request->from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:false;
        $to = $request->to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:false; 
        $status = $request->status? $request->status:false;

        $languages = $this->languages;

        return view('admin.incidences.index')->with(compact('header', 'incidence_status', 'customer', 'from', 'to', 'status', 'languages'));
    }

    /**
     * 1.2. Listado de incidencias para DataTable.
     */
    public function getIncidencesDT(Request $request){
        $customer_id = $request->customer_id;
        $from = $request->from;
        $to = $request->to;
        $status = $request->status;

        $estados = helpers::incidenceStatus();

        $data = Incidence::select('incidences.*', 'companies.name AS company')
        ->join('companies', 'incidences.customer_id', '=', 'companies.id')   
        ->where('incidences.provider_id', session('currentCompany'))
        //Por cliente:
        ->when($customer_id, function($q) use($customer_id){
            return $q->where('incidences.customer_id', $customer_id);
        })
        //Por intervalo fechas (por fecha de creación):
        ->when($from && $to, function($q) use($from, $to){
            return $q->whereBetween('incidences.created_at', [$from, $to]);
        })
        //Por estado:
        ->when($status && $status > 0, function($q) use($status){
            return $q->where('incidences.status', $status);
        })
        ->orderBy('incidences.id', 'DESC');

        return DataTables::of($data)
        ->addColumn('num', function($row){
            $num = $row->incidence_num? $row->incidence_num:($row->incidence_old_num? $row->incidence_num:$row->id);
            return '<a href="/admin/incidences/'.$row->id.'/edit">'.$num.'</a>';
        })
        ->addColumn('concept', function($row){
            return $row->concept;
        })
        ->addColumn('customer', function($row){
            return $row->company;
        })
        ->addColumn('date', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('status', function($row) use($estados){
            if($row->status && isset($estados[$row->status])){
                return $estados[$row->status];
            }else{
                return '';
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.incidences.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['num'])
        ->make(true);
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
        $incidence->status = config('constants.INCIDENCE_STATUS_OPEN_');
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

        //Patrones de numeración de incidencias:
        $patterns = false;
        if(!$incidence->pattern_id){
            $patterns = IncidencePatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        //Estados de incidencia:
        $incidence_status = helpers::incidenceStatus();

        $languages = $this->languages;

        return view('admin.incidences.edit')->with(compact('header', 'incidence', 'tab', 'patterns', 'incidence_status', 'languages')); 
    }

    /**
     * 5. Actualizar incidencia.
     */
    public function update(Request $request, Incidence $incidence){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/incidences/'.$incidence->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        //Número de orden de compra:
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('incidence', $request->pattern_id);
        }

        $incidence_date = $request->incidence_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->incidence_date):$request->incidence_date:NULL;

        if($request->pattern_id){
            if(!empty($patternNum)){
                $incidence->incidence_num = $patternNum['pattern_num'];  
                $incidence->incidence_num_cardinal = $patternNum['num']; 
            }            
            $incidence->pattern_id = $request->pattern_id;
        }
        $incidence->incidence_old_num = $request->incidence_old_num;
        $incidence->incidence_date = $incidence_date;
        $incidence->concept = $request->concept;
        $incidence->observations = $request->observations;
        $incidence->status = $request->status;
        $incidence->save();

        $msg = trans('textos.incidencia_updated');
        return redirect()->route('incidences.edit', $incidence)->with(compact('msg'));
    }

    /**
     * 6. Eliminar incidencia.
     */
    public function destroy(Incidence $incidence){
        $incidence->delete();

        $msg = trans('textos.incidencia_deleted');
        return redirect()->route('incidences.index')->with(compact('msg')); 
    }
}
