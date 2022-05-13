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
use App\Models\DeliveryPatterns;

class DeliveryPatternsController extends Controller{
    /**
     * 1. Patrones de entregas.
     * 1.1. Listado de patrones para DataTable.
     * 2. Formulario nuevo patrón.
     * 3. Guardar nuevo patrón.
     * 4. Editar patrón.
     * 5. Actualizar patrón.
     * 6. Eliminar patrón.
     * 7. Actualizar estado.
     */
    
    private $option = 'logistica';
    private $module = 'logistics';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:logistics');
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
     * 1. Patrones de entregas.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.patrones_entrega',
            'module' => $this->module,
            'slug' => 'delivery-patterns'
        ];    

        return view('admin.delivery_patterns.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de patrones para DataTable.
     */
    public function getPatternsDT(){
        $patterns = DeliveryPatterns::where('company_id', session('currentCompany'))->get(); 

        //Permisos del usuario:
        $editar = Auth::user()->can('delivery-patterns.edit');
        $eliminar = Auth::user()->can('delivery-patterns.destroy');

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
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.patron_nuevo_entregas',
            'module' => $this->module,
            'slug' => 'delivery-patterns'
        ];   

        return view('admin.delivery_patterns.create')->with(compact('header'));    
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
            return redirect('/admin/delivery-patterns/create')
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

        $dp = new DeliveryPatterns();
        $dp->name = $request->input('name');
        $dp->slug = $slug;
        $dp->pattern = $pattern;
        $dp->yearly_reset = $reset;
        $dp->company_id = session('currentCompany');
        $dp->status = $status;
        $dp->save();

        $msg = trans('textos.patron_creado_ok');
        return redirect()->route('order-patterns.index')->with(compact('msg'));    
    }

    /**
     * 4. Editar patrón.
     *
     * @param  \App\DeliveryPatterns $pattern
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryPatterns $pattern){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.patron_editar',
            'module' => $this->module,
            'slug' => 'delivery-patterns'
        ];  

        return view('admin.delivery_patterns.edit')->with(compact('header', 'pattern'));    
    }

    /**
     * 5. Actualizar patrón.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeliveryPatterns $pattern){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/delivery-patterns/'.$pattern->id.'/edit')
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
        return redirect()->route('delivery-patterns.index')->with(compact('msg')); 
    }

    /**
     * 6. Eliminar patrón.
     *
     * @param  str $pattern
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryPatterns $pattern){
        $pattern->delete();

        $msg = trans('textos.patron_deleted');
        return redirect()->route('delivery-patterns.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $pattern = DeliveryPatterns::find($id);
            $pattern->status = $status;
            $pattern->save();

            $resp = ($pattern)? true:false;
            return response()->json($resp);
        }
    }
}
