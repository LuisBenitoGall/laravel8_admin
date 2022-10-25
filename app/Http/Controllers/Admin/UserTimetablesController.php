<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\Employee;
use App\Models\UserTimetables;
use App\Models\WorkUserCalendar;

class UserTimetablesController extends Controller{
    /**
     * 1. Obtener horarios personalizados por empleado.
     * 2. Guardar horario de empleado.
     * 3. Eliminar horario.
     * 4. Actualizar horario de usuario.
     */
    
    private $option = 'horarios';
    private $module = 'timetables';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:timetables');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit-save'])){
            return Validator::make($data, [
                'user_id' => 'required|integer',
                'employee_id' => 'required|integer',
                'from' => 'required'
            ]);
        }
    }

    /**
     * 1. Obtener horarios personalizados por empleado.
     */
    public function show(Request $request, $user_id = false, Employee $employee){
        if($request->ajax() && $user_id){
            //El contrato debe pertenecer al usuario recibido:
            if($employee->user_id != $user_id){
                return response()->json(false); 

            }else{
                $user_timetables = UserTimetables::where('user_id', $user_id)
                ->where('employee_id', $employee->id)
                ->get();

                //Ampliación de información de los objetos:
                if($user_timetables){
                    foreach($user_timetables as $ut){
                        $ut->daily_hours = $ut->daily_hours? unserialize($ut->daily_hours):false;

                        //Enviamos las fechas ya formateadas:
                        $ut->desde = $ut->from? Carbon::parse($ut->from)->format($this->languages[session()->get('locale')][4]):'';
                        $ut->hasta = $ut->to? $ut->to != config('constants.UNDEFINED_DATE_')? Carbon::parse($ut->to)->format($this->languages[session()->get('locale')][4]):'':'';
                    }
                }

                $employee->from = $employee->start_date? Carbon::parse($employee->start_date)->format($this->languages[session()->get('locale')][4]):false;
                $employee->to = $employee->end_date && $employee->end_date != config('constants.UNDEFINED_DATE_')? Carbon::parse($employee->end_date)->format($this->languages[session()->get('locale')][4]):false;

                $data = [$employee, $user_timetables];

                return response()->json($data);
            }
        }
    }

    /**
     * 2. Guardar horario de empleado.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-custom_timetables');

            return redirect('/admin/employees/'.$request->user_id.'/user')
                ->withErrors($validator)
                ->withInput();
        }

        //Formatear fechas:
        $from = ($request->input('from') != '')? (config('app.locale') != 'en')? Helpers::convertFecha($request->input('from'), false):$request->input('from'):NULL;
        $to = ($request->input('to') && $request->input('to') != '')? (config('app.locale') != 'en')? Helpers::convertFecha($request->input('to'), false):$request->input('to'):config('constants.UNDEFINED_DATE_');

        $arrHours = [];
        $arrNotWorkingDays = [];
        $arrNotWorkingDaysIndex = [];
        for($i=1; $i<=7; $i++){
            $jornada = 'jornada_'.$i;
            $arrHours[$i] = $request->input($jornada);

            $no_working_day = 'day_type_'.$i;
            $arrNotWorkingDays[$i] = $request->input($no_working_day);
            if($request->input($no_working_day)){
                array_push($arrNotWorkingDaysIndex, $i);
                $arrHours[$i] = '_'.$request->input($no_working_day);
            }
        }
        $hours = serialize($arrHours);

        //Si existen jornada no laborables en el calendario personalizado del empleado, se guardan las fechas en work_user_calendars
        if(count($arrNotWorkingDays) > 0){
            //Info del contrato de empleado:
            $employee = Employee::select('id', 'start_date', 'end_date')->find($request->employee_id);
            if($employee->end_date && $employee->end_date != config('constants.UNDEFINED_DATE_')){
                $limit = $employee->end_date;
            }else{
                $year_limit = date('Y', strtotime($employee->start_date)) + config('constants.NUM_OF_YEARS_');
                $limit = $year_limit.'-12-31';
            }

            $from_ = strtotime($employee->start_date);
            $to_ = strtotime($limit);

            //Recorremos todo el rango de fechas determinado y buscamos los días (día de semana) en que se produce la jornada no laborable:
            for($i=$from_; $i<=$to_; $i+=86400){
                //Día de la semana:
                $week_day = date('N', $i);

                //Si existe, lo guardamos
                if(in_array($week_day, $arrNotWorkingDaysIndex)){
                    $day_type_id = $arrNotWorkingDays[$week_day];
                    $fecha = date('Y-m-d', $i);
                    $status = $fecha <= date('Y-m-d')? 1:0;

                    $wuc = WorkUserCalendar::where('user_id', $request->user_id)
                    ->where('date', $fecha)
                    ->first();

                    if($wuc){
                        $wuc->day_type_id = $day_type_id;
                        $wuc->save();

                    }else{
                        $wuc = new WorkUserCalendar();
                        $wuc->user_id = $request->user_id;
                        $wuc->day_type_id = $day_type_id;
                        $wuc->date = $fecha;
                        $wuc->status = $status;
                        $wuc->save();
                    }
                }
            }
        }

        //Comprobando que no haya incompatibilidad de fechas, ninguna fecha puede solaparse con otra:
        $timetables = UserTimetables::where('employee_id', $request->employee_id)->get();
        if($timetables->count() > 0){
            $from_ = strtotime($from);
            $to_ = strtotime($to);

            foreach($timetables as $tm){
                if( ($request->ut_id && $tm->id != $request->ut_id) || !$request->ut_id){
                    $desde = strtotime($tm->from);
                    $hasta = $tm->to? strtotime($tm->to):strtotime(config('constants.UNDEFINED_DATE_'));
                    if( ($from_ >= $desde && $from_ <= $hasta) || ($to_ >= $desde && $to_ <= $hasta) ){
                        $alert = trans('textos.registro_horario_incompatible_warning');
                        return redirect()->route('employees.user', $request->user_id)->with(compact('alert'));
                        exit;   
                    }
                }
            }
        }

        //Insert...
        if(!$request->ut_id){
            $ut = new UserTimetables();
            $ut->user_id = $request->user_id;
            $ut->employee_id = $request->employee_id;
            $ut->from = $from;
            $ut->to = $to;
            $ut->daily_hours = $hours;
            $ut->save();

        //... or update:
        }else{
            $ut = UserTimetables::find($request->ut_id);    
            $ut->from = $from;
            $ut->to = $to;
            $ut->daily_hours = $hours;
            $ut->save();
        }

        $msg = trans('textos.registro_horario_updated');
        return redirect()->route('employees.user', $request->user_id)->with(compact('msg'));    
    }

    /**
     * 3. Eliminar horario.
     */
    public function delete(UserTimetables $ut){
        $user_id = $ut->user_id;

        $ut->delete();
        
        $msg = trans('textos.registro_horario_deleted');
        return redirect()->route('employees.user', $user_id)->with(compact('msg'));
    }

    /**
     * 4. Actualizar horario de usuario.
     */
    public function update(Request $request){
        dd($request->all());
    }

}
