<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\SatPatterns;

class SatPatternsController extends Controller{
    /**
     * 1. Patrones de SAT.
     * 1.1. Listado de patrones para DataTable. 
     * 2. Formulario nuevo patrón.
     * 3. Guardar nuevo patrón.
     * 4. Editar patrón.
     * 5. Actualizar patrón.
     * 6. Eliminar patrón.
     * 7. Actualizar estado.
     */   
    
    private $option = 'servicios_asistencia_tecnica';
    private $module = 'sat';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:sat');
        $this->middleware('sat_pattern_get', ['only' => ['edit', 'update', 'destroy']]);
    }  

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);
        }
    } 

    /**
     * 1. Patrones de referencia de pedido.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.sat_patrones_numeracion',
            'module' => $this->module,
            'slug' => 'sat-patterns'
        ];    

        return view('admin.sat_patterns.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de patrones para DataTable.
     */
    public function getPatternsDT(){
        $patterns = SatPatterns::where('company_id', session('currentCompany'))->get(); 

        //Permisos del usuario:
        $editar = Auth::user()->can('sat-patterns.edit');
        $eliminar = Auth::user()->can('sat-patterns.destroy');

        //Ampliación de información de los objetos:
        foreach($patterns as $row){
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            $arr = unserialize($row->pattern);
            $row->patron = '';
            foreach($arr as $p){
                $row->patron .= $p;
            }

            //Permisos para las acciones:
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
            'slug' => 'sat-patterns'
        ];   

        return view('admin.sat_patterns.create')->with(compact('header'));
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
            return redirect('/admin/sat-patterns/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Sustituimos los segmentos de texto por el valor otorgado:
        $i = 0;
        $segments = [];
        foreach($request->input('segment') as $row){
            if($row == 'text'){
                array_push($segments, $request->input('segment_text')[$i]); 
                $i++;
            }elseif($row == 'digits'){
                //Concatenamos el número de dígitos que se mostrarán representados con 0.
                $ndigits = '';
                for($j=0; $j<$request->ndigits; $j++){
                    $ndigits = $ndigits.'0';
                }

                array_push($segments, '|'.$ndigits.'|');   
            }else{
                array_push($segments, '|'.$row.'|');    
            } 
        }
        $pattern = serialize($segments);

        //Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status'))? 1:0;
        $reset = $request->input('yearly_reset')? 1:0;

        $bp = new SatPatterns();
        $bp->name = $request->input('name');
        $bp->slug = $slug;
        $bp->pattern = $pattern;
        $bp->yearly_reset = $reset;
        $bp->company_id = session('currentCompany');
        $bp->status = $status;
        $bp->save();

        $msg = trans('textos.patron_creado_ok');
        return redirect()->route('sat-patterns.index')->with(compact('msg'));    
    }

    /**
     * 4. Editar patrón.
     *
     * @param  \App\SatPatterns $pattern
     * @return \Illuminate\Http\Response
     */
    public function edit(SatPatterns $pattern){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.patron_editar',
            'module' => $this->module,
            'slug' => 'sat-patterns'
        ];  

        return view('admin.sat_patterns.edit')->with(compact('header', 'pattern'));    
    }

    /**
     * 5. Actualizar patrón.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SatPatterns $pattern){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/sat-patterns/'.$pattern->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status'))? 1:0;

        $pattern->name = $request->input('name');
        $pattern->slug = $slug;
        $pattern->status = $status;
        $pattern->save();

        $msg = trans('textos.patron_updated');
        return redirect()->route('sat-patterns.index')->with(compact('msg')); 
    }

    /**
     * 6. Eliminar patrón.
     *
     * @param  str $pattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(SatPatterns $pattern){
        $pattern->delete();

        $msg = trans('textos.patron_deleted');
        return redirect()->route('sat-patterns.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $pattern = SatPatterns::find($id);
            $pattern->status = $status;
            $pattern->save();

            $resp = ($pattern)? true:false;
            return response()->json($resp);
        }
    }  
}
