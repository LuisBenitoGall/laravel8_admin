<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Company;
use App\Models\DayTypes;
use App\Models\DayTypesCriterions;

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
     * 8. Obtener day types por empresa.
     * 9. Establecer criterios de calendario para tipo de jornada.
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

        //Validación update:
        }elseif(isset($data['submit_update'])){
          return Validator::make($data, [
                'name' => 'required|string|max:150'
            ]);

        //Validación set criterions:
        }elseif(isset($data['submit_set_criterion'])){
          return Validator::make($data, [
                'criterion' => 'required|string'
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

        //$criterions = ['-' => ucfirst(trans('textos.criterio_ninguno'))];  //Iniciamos con opción para descartar todo criterio.
        $arr = DayTypesCriterions::select('id', 'name')
        ->pluck('name', 'id')
        ->toArray();

        //array_unshift($criterions, ucfirst(trans('textos.criterio_ninguno')));
        $no_criterion[0] = ucfirst(trans('textos.criterio_ninguno'));

        $criterions = $no_criterion + $arr;

        return view('admin.day_types.index')->with(compact('header', 'criterions'));  
    }

    /**
     * 1.1. Listado de tipos de jornada para DataTable.
     */
    public function getDayTypesDT(){
        $data = DayTypes::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC');

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name; 
        })
        ->addColumn('color', function($row){
            return '<span class="colorEx mr-2" style="background-color: '.$row->color.';"></span> '.$row->color;     
        })
        ->addColumn('is_working', function($row){
            if($row->is_working == 1){
                return '<i class="fa fa-check text-success" rel="tooltip" title="'.trans('textos.compatible_laborable').'"></i>';
            }else{
                return '<i class="fa fa-times text-danger" rel="tooltip" title="'.trans('textos.compatible_no').'"></i>';
            }     
        })
        ->addColumn('user_enabled', function($row){
            if($row->user_enabled == 1){
                return '<i class="fa fa-check text-success" rel="tooltip" title="'.trans('textos.disponible_para_usuario').'"></i>';
            }else{
                return '<i class="fa fa-times text-danger" rel="tooltip" title="'.trans('textos.disponible_no').'"></i>';
            }
        })
        ->addColumn('cumulative', function($row){
            if($row->cumulative == 1){
                return '<i class="fa fa-check text-success" rel="tooltip" title="'.trans('textos.acumulable').'"></i>';
            }else{
                return '<i class="fa fa-times text-danger" rel="tooltip" title="'.trans('textos.acumulable_no').'"></i>';
            }
        })
        ->addColumn('modifiable', function($row){
            if($row->modifiable == 1){
                return '<i class="fa fa-check text-success" rel="tooltip" title="'.trans('textos.modificable').'"></i>';
            }else{
                return '<i class="fa fa-times text-danger" rel="tooltip" title="'.trans('textos.modificable_no').'"></i>';
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.day_types.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['color', 'is_working', 'user_enabled', 'cumulative', 'modifiable'])
        ->make(true);
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
        $in_office = $request->input('in_office')? 1:0;
        $user_enabled = $request->input('user_enabled')? 1:0;
        $cumulative = $request->input('cumulative')? 1:0;
        $rank = $request->input('rank')? 1:0;
        $modifiable = $request->input('modifiable')? 1:0;

        $daytype = new DayTypes();
        $daytype->company_id = session('currentCompany');
        $daytype->name = ucfirst($request->input('name'));
        $daytype->color = $request->input('color');
        $daytype->is_working = $is_working;
        $daytype->in_office = $in_office;
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

    /**
     * 8. Obtener day types por empresa.
     */
    public function getCompanyDayTypes(Request $request, $company_id){
        if($request->ajax()){
            $data = DayTypes::select('id', 'name')
            ->where('company_id', $company_id)
            ->get();

            return response()->json($data);

        }else{
            return response()->json(false);
        }
    }

    /**
     * 9. Establecer criterios de calendario para tipo de jornada.
     */
    public function setCriterions(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/day-types')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->criterion > 0){
            DayTypes::where('id', $request->id)
            ->update(['criterion_id' => $request->criterion]);
        }else{
            DayTypes::where('id', $request->id)
            ->update(['criterion_id' => NULL]);    
        }

        $msg = trans('textos.criterio_establecido');
        return redirect()->route('day-types.index')->with(compact('msg'));
    }
}
