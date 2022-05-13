<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\DayTypes;
use App\Models\DayTypesConfig;

class DayTypesConfigController extends Controller{
    /**
     * 1. Configuraciones por tipo de jornada.
     * 1.1. Configuración por tipo de jornada y año.
     * 2. Guardar configuración de tipo de jornada.
     * 3. Eliminar configuración.
     */
    
    private $option = 'calendario';
    private $module = 'calendar';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:calendar');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'year' => 'required|numeric|max:3000'
            ]);
        }
    }

    /**
     * 1. Configuraciones por tipo de jornada.
     */
    public function index(Request $request, $type = false){
        if($request->ajax() && $type){
            $data = DayTypesConfig::where('day_type_id', $type)
            ->orderBy('year', 'DESC')
            ->get();

            return response()->json($data);
        }
    }

    /**
     * 1.1. Configuración por tipo de jornada y año.
     */
    public function show(Request $request, $type, $year){
        if($request->ajax() && $type && $year){
            $data = DayTypesConfig::where('day_type_id', $type)
            ->where('year', $year)
            ->first();  

            return response()->json($data);  
        }    
    }

    /**
     * 2. Guardar configuración de tipo de jornada.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-config-day-types');
            
            return redirect('admin/day-types')
                ->withErrors($validator)
                ->withInput();
        }

        $ranks = NULL;

        //Componemos las fechas de los rangos, y verificamos que no se solapen entre ellas:
        if($request->day_from && $request->month_from){
            $from_dates = [];
            $to_dates = [];

            $i = 0;
            foreach($request->day_from as $df){
                if($request->month_from[$i] && $request->day_to[$i] && $request->month_to[$i]){
                    $day_from = $df < 10? '0'.$df:$df;
                    $day_to = $request->day_to[$i] < 10? '0'.$request->day_to[$i]:$request->day_to[$i];

                    $from = $request->year.'-'.$request->month_from[$i].'-'.$day_from;
                    $to = $request->year.'-'.$request->month_to[$i].'-'.$day_to;

                    //La fecha fin no puede ser inferior a la fecha de inicio:
                    if($from > $to){
                        $alert = trans('textos.fecha_inicio_superior_error');
                        return redirect()->route('day-types.index')->with(compact('alert'));
                    }

                    array_push($from_dates, $from);
                    array_push($to_dates, $to);
                }
                $i++;
            }

            //Buscamos intersección de fechas:
            $intersection = false;
            if(count($from_dates) > 1){
                foreach($from_dates as $idx => $fd){
                    foreach($from_dates as $idx_ => $fd_){
                        if($idx != $idx_ && isset($from_dates[$idx_+1])){
                            $intersection = Helpers::shareDateDays($from_dates[$idx], $to_dates[$idx], $from_dates[$idx_], $to_dates[$idx_]);

                            if($intersection){
                                break;
                            }
                        }
                    }
                }
            }

            if($intersection){
                $alert = trans('textos.rango_intersecciones_error');
                return redirect()->route('day-types.index')->with(compact('alert'));   

            //Creamos string con los rangos válidos: 
            }else{
                $l = 0;
                foreach($from_dates as $rank){
                    if($l > 0){
                        $ranks .= ',';
                    }
                    $ranks .= $from_dates[$l].'/'.$to_dates[$l];     
                    $l++;
                }    
            }
        }

        //Comprobamos que no exista previamente:
        $exist = DayTypesConfig::where('day_type_id', $request->input('id'))
        ->where('year', $request->input('year'))
        ->first();

        if($exist){
            $exist->min = $request->input('min');
            $exist->max = $request->input('max');
            $exist->max_per_month = $request->input('max_per_month');
            $exist->save();

        }else{
            $d = new DayTypesConfig();
            $d->day_type_id = $request->input('id');
            $d->year = $request->input('year');
            $d->min = $request->input('min');
            $d->max = $request->input('max');
            $d->max_per_month = $request->input('max_per_month');
            $d->save(); 
        }

        $msg = trans('textos.jornada_config_ok');
        return redirect()->route('day-types.index')->with(compact('msg'));
    }

    /**
     * 3. Eliminar configuración.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DayTypesConfig $config){
        $config->delete();

        $msg = trans('textos.jornada_config_delete');
        return redirect()->route('day-types.index')->with(compact('msg'));
    }
}
