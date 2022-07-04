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
use App\Models\BatchPatterns;

class BatchPatternsController extends Controller{
    /**
     * 1. Patrones de numeración de facturas.
     * 1.1. Listado de patrones para DataTable. 
     * 2. Formulario nuevo patrón.
     * 3. Guardar nuevo patrón.
     * 4. Editar patrón.
     * 5. Actualizar patrón.
     * 6. Eliminar patrón.
     * 7. Actualizar estado.
     */   
    
    private $option = 'produccion';
    private $module = 'production';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:production');
        $this->middleware('batch_pattern_get', ['only' => ['edit', 'update', 'destroy']]);
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
     * 1. Patrones de numeración de facturas.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.lotes_patrones',
            'module' => $this->module,
            'slug' => 'batch-patterns'
        ];    

        return view('admin.batch_patterns.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de patrones para DataTable.
     */
    public function getPatternsDT(){
        $patterns = BatchPatterns::where('company_id', session('currentCompany')); 

        return DataTables::of($patterns)
        ->addColumn('patron', function($pattern){
            $arr = unserialize($pattern->pattern);
            $patron = '';
            foreach($arr as $p){
                $patron .= $p;
            }
            return $patron;
        })
        ->addColumn('reset', function($pattern){
            return view('admin.batch_patterns.partials.dt-reset', compact('pattern'));    
        })
        ->addColumn('alta', function($pattern){
            return $pattern->created_at->format($this->languages[session()->get('locale')][4]);    
        })
        ->addColumn('acciones', function($pattern){
            return view('admin.batch_patterns.partials.dt-actions', compact('pattern'));    
        })
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
            'suboption' => 'textos.lote_patron_nuevo',
            'module' => $this->module,
            'slug' => 'batch-patterns'
        ];   

        return view('admin.batch_patterns.create')->with(compact('header'));
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
            return redirect('/admin/batch-patterns/create')
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

        $bp = new BatchPatterns();
        $bp->name = $request->input('name');
        $bp->slug = $slug;
        $bp->pattern = $pattern;
        $bp->yearly_reset = $reset;
        $bp->company_id = session('currentCompany');
        $bp->status = $status;
        $bp->save();

        $msg = trans('textos.patron_creado_ok');
        return redirect()->route('batch-patterns.index')->with(compact('msg'));    
    }

    /**
     * 4. Editar patrón.
     *
     * @param  \App\BatchPatterns $pattern
     * @return \Illuminate\Http\Response
     */
    public function edit(BatchPatterns $pattern){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.patron_editar',
            'module' => $this->module,
            'slug' => 'batch-patterns'
        ];  

        return view('admin.batch_patterns.edit')->with(compact('header', 'pattern'));    
    }

    /**
     * 5. Actualizar patrón.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BatchPatterns $pattern){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/batch-patterns/'.$pattern->id.'/edit')
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
        return redirect()->route('batch-patterns.index')->with(compact('msg')); 
    }

    /**
     * 6. Eliminar patrón.
     *
     * @param  str $pattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(BatchPatterns $pattern){
        $pattern->delete();

        $msg = trans('textos.patron_deleted');
        return redirect()->route('batch-patterns.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $pattern = BatchPatterns::find($id);
            $pattern->status = $status;
            $pattern->save();

            $resp = ($pattern)? true:false;
            return response()->json($resp);
        }
    }    
}
