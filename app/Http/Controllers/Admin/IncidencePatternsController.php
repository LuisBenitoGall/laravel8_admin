<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

// Models:
use App\Models\IncidencePatterns; 

class IncidencePatternsController extends Controller{
    /**
     * 1. Patrones de referencia de incidencia.
     * 1.1. Listado de patrones para DataTable.
     * 2. Formulario nuevo patrón.
     * 3. Guardar nuevo patrón.
     * 4. Editar patrón.
     * 5. Actualizar patrón.
     * 6. Eliminar patrón.
     * 7. Actualizar estado.
     */
    
    private $option = 'incidencias';  
    private $module = 'quality';
    private $languages;

    public function __construct(){
        // Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:quality');
        $this->middleware('incidence_pattern_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    // 0. Validator:
    protected function validator(array $data){
        return Validator::make($data, [
            'name' => 'required|string|max:255'
        ]);
    } 

    /**
     * 1. Patrones de referencia de incidencia.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.incidencias_patrones',
            'module' => $this->module,
            'slug' => 'incidence-patterns'
        ];    

        return view('admin.incidence_patterns.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de patrones para DataTable.
     */
    public function getPatternsDT(){
        $patterns = IncidencePatterns::where('company_id', session('currentCompany'))->get(); 

        // Permisos del usuario:
        $editar = Auth::user()->can('incidence-patterns.edit');
        $eliminar = Auth::user()->can('incidence-patterns.destroy');

        // Ampliación de información de los objetos:
        foreach($patterns as $row){
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            $arr = unserialize($row->pattern);
            $row->patron = '';
            foreach($arr as $p){
                $row->patron .= $p;
            }

            // Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;
        }

        return datatables($patterns)->toJson();
    }

    /**
     * 2. Formulario nuevo patrón.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.patron_nuevo',
            'module' => $this->module,
            'slug' => 'incidence-patterns'
        ];   

        return view('admin.incidence_patterns.create')->with(compact('header'));
    }

    /**
     * 3. Guardar nuevo patrón.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/incidence-patterns/create')
                ->withErrors($validator)
                ->withInput();
        }

        // Verificamos que al menos exista un segmento de dígitos:
        $digits = false;
        foreach($request->segment as $s){
            if($s == 'digits'){
                $digits = true;
                break;
            }
        }

        if(!$digits){
            $alert = trans('textos.patron_sin_digitos');
            return redirect()->route('incidence-patterns.create')->with(compact('alert'));
            exit;
        }

        // Sustituimos los segmentos de texto por el valor otorgado:
        $i = 0;
        $segments = [];
        foreach($request->segment as $row){
            if($row == 'text'){
                array_push($segments, $request->input('segment_text')[$i]); 
                $i++;
            } elseif($row == 'digits'){
                // Concatenamos el número de dígitos que se mostrarán representados con 0.
                $ndigits = '';
                for($j=0; $j<$request->ndigits; $j++){
                    $ndigits .= '0';
                }

                array_push($segments, '|'.$ndigits.'|');   
            } else {
                array_push($segments, '|'.$row.'|');    
            } 
        }
        $pattern = serialize($segments);

        // Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status')) ? 1 : 0;

        $ip = new IncidencePatterns();
        $ip->name = $request->input('name');
        $ip->slug = $slug;
        $ip->pattern = $pattern;
        $ip->company_id = session('currentCompany');
        $ip->status = $status;
        $ip->save();

        $msg = trans('textos.patron_creado_ok');
        return redirect()->route('incidence-patterns.index')->with(compact('msg'));   
    }

    /**
     * 4. Editar patrón.
     *
     * @param  \App\IncidencePatterns $pattern
     * @return \Illuminate\Http\Response
     */
    public function edit(IncidencePatterns $pattern){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.patron_editar',
            'module' => $this->module,
            'slug' => 'incidence-patterns'
        ];

        return view('admin.incidence_patterns.edit')->with(compact('header', 'pattern'));    
    }

    /**
     * 5. Actualizar patrón.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IncidencePatterns $pattern){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/incidence-patterns/'.$pattern->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        // Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status')) ? 1 : 0;

        $pattern->name = $request->name;
        $pattern->slug = $slug;
        $pattern->status = $status;
        $pattern->save();

        $msg = trans('textos.patron_updated');
        return redirect()->route('incidence-patterns.index')->with(compact('msg')); 
    }

    /**
     * 6. Eliminar patrón.
     *
     * @param  IncidencePatterns $pattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(IncidencePatterns $pattern){
        $pattern->delete();

        $msg = trans('textos.patron_deleted');
        return redirect()->route('incidence-patterns.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $pattern = IncidencePatterns::find($id);
            $pattern->status = $status;
            $pattern->save();

            $resp = ($pattern) ? true : false;
            return response()->json($resp);
        }
    }
}
