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

//Models:
use App\Models\RemittancePattern;

class RemittancePatternController extends Controller{
    /**
     * 1. Patrones de numeración de remesas.
     * 1.1. Listado de patrones para DataTable. 
     * 2. Formulario nuevo patrón.
     * 3. Guardar nuevo patrón.
     * 4. Editar patrón.
     * 5. Actualizar patrón.
     * 6. Eliminar patrón.
     * 7. Actualizar estado.
     */   
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:accounting');
        $this->middleware('remittance_pattern_get', ['only' => ['edit', 'update', 'destroy']]);
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
     * 1. Patrones de numeración de remesas.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.remesa_patrones',
            'module' => $this->module,
            'slug' => 'remittance-patterns'
        ];    

        return view('admin.remittance_patterns.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de patrones para DataTable.
     */
    public function getPatternsDT(){
        $data = RemittancePattern::where('company_id', session('currentCompany')); 

        return DataTables::of($data)
        ->addColumn('reset', function($row){
            if($row->yearly_reset == 1){
                return '<i class="fa fa-check text-success"></i>';
            }else{
                return '<i class="fa fa-ban text-danger"></i>';    
            }
        })
        ->addColumn('pattern', function($row){
            $arr = unserialize($row->pattern);
            $pattern = '';
            foreach($arr as $p){
                $pattern .= $p;
            }
            return $pattern;
        })
        ->addColumn('created_at', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);
        })  
        ->addColumn('acciones', function($row){
            return view('admin.remittance_patterns.partials.dt-actions', compact('row')); 
        })
        ->rawColumns(['reset'])
        ->make(true);
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
            'slug' => 'remittance-patterns'
        ];   

        return view('admin.remittance_patterns.create')->with(compact('header'));
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
            return redirect('/admin/remittance-patterns/create')
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

        $bp = new RemittancePattern();
        $bp->name = $request->input('name');
        $bp->slug = $slug;
        $bp->pattern = $pattern;
        $bp->yearly_reset = $reset;
        $bp->company_id = session('currentCompany');
        $bp->status = $status;
        $bp->save();

        $msg = trans('textos.patron_creado_ok');
        return redirect()->route('remittance-patterns.index')->with(compact('msg'));    
    }

    /**
     * 4. Editar patrón.
     *
     * @param  \App\RemittancePattern $pattern
     * @return \Illuminate\Http\Response
     */
    public function edit(RemittancePattern $pattern){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.patron_editar',
            'module' => $this->module,
            'slug' => 'remittance-patterns'
        ];  

        return view('admin.remittance_patterns.edit')->with(compact('header', 'pattern'));    
    }

    /**
     * 5. Actualizar patrón.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RemittancePattern $pattern){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/remittance-patterns/'.$pattern->id.'/edit')
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
        return redirect()->route('remittance-patterns.index')->with(compact('msg')); 
    }

    /**
     * 6. Eliminar patrón.
     *
     * @param  str $pattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(RemittancePattern $pattern){
        $pattern->delete();

        $msg = trans('textos.patron_deleted');
        return redirect()->route('remittance-patterns.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $pattern = RemittancePattern::find($id);
            $pattern->status = $status;
            $pattern->save();

            $resp = ($pattern)? true:false;
            return response()->json($resp);
        }
    }        
}
