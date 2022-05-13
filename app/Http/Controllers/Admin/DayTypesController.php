<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Company;
use App\Models\DayTypes;

class DayTypesController extends Controller{
    /**
     * 1. Listado de tipos de jornada.
     * 1.1. Listado de tipos de jornada para DataTable. 
     * 2. Formulario nuevo tipo de jornada.
     * 3. Guardar nuevo tipo de jornada.
     * 4. Editar tipo de jornada.
     * 5. Actualizar tipo de jornada.
     * 6. Eliminar tipo de jornada.
     * 7. Exportar valores a otras empresas vinculadas.
     */
    
    private $option = 'calendario';
    private $module = 'calendar';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:calendar');
        $this->middleware('day_types_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:150'
            ]);
        }elseif(isset($data['submit_update'])){
          return Validator::make($data, [
                'name' => 'required|string|max:150'
            ]);
        }
    } 

    /**
     * 1. Listado de tipos de jornada.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.tipos_jornada',
            'module' => $this->module,
            'slug' => 'day-types'
        ]; 

        return view('admin.day_types.index')->with(compact('header'));  
    }

    /**
     * 1.1. Listado de tipos de jornada para DataTable.
     */
    public function getDayTypesDT(){
        $day_types = DayTypes::where('company_id', session('currentCompany'))
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('day-types.edit');
        $eliminar = Auth::user()->can('day-types.destroy');

        foreach($day_types as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;    
        }

        return datatables($day_types)->toJson();
    }

    /**
     * 2. Formulario nuevo tipo de jornada.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.tipo_jornada_nuevo',
            'module' => $this->module,
            'slug' => 'day-types'
        ]; 

        return view('admin.day_types.create')->with(compact('header'));      
    }

    /**
     * 3. Guardar nuevo tipo de jornada.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/day-types/create')
                ->withErrors($validator)
                ->withInput();
        }

        $is_working = $request->input('is_working')? 1:0;
        $user_enabled = $request->input('user_enabled')? 1:0;
        $cumulative = $request->input('cumulative')? 1:0;
        $rank = $request->input('rank')? 1:0;
        $modifiable = $request->input('modifiable')? 1:0;

        $daytype = new DayTypes();
        $daytype->company_id = session('currentCompany');
        $daytype->name = ucfirst($request->input('name'));
        $daytype->color = $request->input('color');
        $daytype->is_working = $is_working;
        $daytype->user_enabled = $user_enabled;
        $daytype->cumulative = $cumulative;
        $daytype->rank = $rank;
        $daytype->modifiable = $modifiable;
        $daytype->save();

        $msg = trans('textos.jornada_ok');
        return redirect()->route('day-types.index')->with(compact('msg'));  
    }

    /**
     * 4. Editar tipo de jornada.
     */
    public function edit(DayTypes $dt){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.tipo_jornada_editar',
            'module' => $this->module,
            'slug' => 'day-types'
        ]; 

        return view('admin.day_types.edit')->with(compact('header', 'dt'));    
    }

    /**
     * 5. Actualizar tipo de jornada.
     */
    public function update(Request $request, DayTypes $dt){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/day-types/'.$dt->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $cumulative = $request->input('cumulative')? 1:0;
        $rank = $request->input('rank')? 1:0;
        $modifiable = $request->input('modifiable')? 1:0;

        $dt->name = $request->input('name');
        $dt->color = $request->input('color');
        $dt->cumulative = $cumulative;
        $dt->rank = $rank;
        $dt->modifiable = $modifiable;
        $dt->save();

        $msg = trans('textos.jornada_update');
        return redirect()->route('day-types.edit', $dt)->with(compact('msg'));  
    }

    /**
     * 6. Eliminar tipo de jornada.
     */
    public function destroy(DayTypes $dt){
        $dt->delete();

        $msg = trans('textos.jornada_eliminado');
        return redirect()->route('day-types.index')->with(compact('msg'));
    }

    /**
     * 7. Exportar valores a otras empresas vinculadas.
     */
    public function export(Request $request){
        if(is_array($request->companies) && count($request->companies) > 0){
            $data = DayTypes::where('company_id', session('currentCompany'))
            ->get();

            if($data->count() > 0){
                foreach($request->companies as $co){
                    foreach($data as $row){
                        //Comprobamos que no exista el registro:
                        $exist = DayTypes::select('id')
                        ->where('company_id', $co)
                        ->where('name', $row->name)
                        ->first();

                        if(!$exist){
                            $dt = new DayTypes();
                            $dt->company_id = $co;
                            $dt->name = $row->name;
                            $dt->color = $row->color;
                            $dt->is_working = $row->is_working;
                            $dt->working_day = $row->working_day;
                            $dt->user_enabled = $row->user_enabled;
                            $dt->cumulative = $row->cumulative;
                            $dt->rank = $row->rank;
                            $dt->modifiable = $row->modifiable;
                            $dt->save();
                        }
                    }
                }

                $msg = trans('textos.exportacion_ok');
                return redirect()->route('day-types.index')->with(compact('msg'));

            }else{
                $alert = trans('textos.exportacion_vacia');
            }

        }else{
            $alert = trans('textos.exportacion_vacia');
        }

        return redirect()->route('day-types.index')->with(compact('alert'));
    }
}
