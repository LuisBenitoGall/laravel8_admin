<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\WorkCalendar;
use App\Models\Workplace;

class WorkCalendarController extends Controller{
    /**
     * 1. Calendario laboral de la empresa.
     * 2. Calendario por filtro.
     * 3. Guardar intervalos de horarios laborables.
     * 4. Guardar horario laborable.
     * 5. Copiar calendario laboral entre empresas y centros de trabajo.
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
        //Validación save intervals:
        if(isset($data['submit_save_intervals'])){
            return Validator::make($data, [
                'workplace_id' => 'required|integer',
                'year' => 'required|integer',
                'from' => 'required',
                'hours' => 'required|date_format:H:i'
            ],[
                'hours.date_format' => trans('textos.hora_formato_correcto_aviso')
            ]); 

        //Validación copiar calendario:
        }elseif(isset($data['submit_copy'])){
            return Validator::make($data, [
                'workplace_id' => 'required|integer',
                'year' => 'required|integer'
            ]);
        }
    }

    /**
     * 1. Calendario laboral de la empresa.
     */
    public function index($workplace_id = false, $year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.calendario_laboral_empresa',
            'module' => $this->module,
            'slug' => 'work-calendar'
        ]; 

        //Centros de trabajo:
        $workplaces = Workplace::select('workplaces.id', 'workplaces.name', 'towns.name AS Town')
        ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
        ->where('workplaces.company_id', session('currentCompany'))
        ->where('workplaces.status', 1)
        ->orderBy('workplaces.name', 'ASC')
        ->get();

        $year = $year? $year:date('Y');

        if($workplaces->count() == 1 && !$workplace_id){
            $workplace = Workplace::find($workplaces[0]->id);   
        }elseif($workplace_id){
            $workplace = Workplace::find($workplace_id);
        }else{
            $workplace = false;
        }   

        //Comprobamos que el centro de trabajo pertenezca a la empresa en sesión:
        if($workplace && $workplace->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('work-calendar.index')->with(compact('alert'));
            exit;    
        }

        $company = $workplace? Company::find($workplace->company_id):false;

        //Días de la semana:
        $weekDays = helpers::weekDays();

        $languages = $this->languages;

        return view('admin.work_calendar.index')->with(compact('header', 'year', 'workplaces', 'workplace', 'company', 'weekDays', 'languages'));    
    }

    /**
     * 2. Calendario por filtro.
     */
    public function calendarBy(Request $request){
        if($request->workplace_id){
            return redirect()->route('work-calendar.index', [$request->workplace_id, $request->year]);  
        }else{
            return redirect()->route('work-calendar.index');
        }       
    }

    /**
     * 3. Guardar intervalos de horarios laborables.
     */
    public function storeIntervals(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-working-hours');
            return redirect('/admin/work-calendar/'.$request->workplace_id.'/'.$request->year)
                ->withErrors($validator)
                ->withInput();
        }

        //Formateo las fechas a inglés:
        $from = $request->input('from')? Helpers::convertFecha($request->input('from')):false;
        //Si no viene declarada la fecha fin, se le pasa la fecha de inicio:
        $to = $request->input('to')? Helpers::convertFecha($request->input('to')):$from;

        //Días de la semana afectados:
        $weekDays = $request->weekdays? $request->weekdays:[1,2,3,4,5,6,7];

        if($from <= $to){
            for($i = $from; $i<=$to; $i = date("Y-m-d", strtotime($i ."+ 1 days"))){
                //Comprobamos que no sea fin de semana. Valores 6 y 7 corresponden a sábado y domingo:
                $weekDay = date('N', strtotime($i));

                $data['workplace_id'] = $request->workplace_id;
                $data['day_type_id'] = NULL;
                $data['date'] = $i;
                $data['weekdays'] = $weekDays;
                $data['weekDay'] = $weekDay;
                $data['hours'] = $request->hours;
                $this->store($data);

                //Copia para otros centros de trabajo:
                if($request->workplaces){
                    foreach($request->workplaces as $wp){
                        $data['workplace_id'] = $wp;
                        $data['day_type_id'] = NULL;
                        $data['date'] = $i;
                        $data['weekdays'] = $weekDays;
                        $data['weekDay'] = $weekDay;
                        $data['hours'] = $request->hours;
                        $this->store($data);    
                    }
                }
            }

            $msg = trans('textos.horarios_laborables_saved');
            return redirect()->route('work-calendar.index', [$request->input('workplace_id'), $request->year])->with(compact('msg'));

        }else{
            $alert = trans('textos.fecha_final_inferior_aviso');
            return redirect()->route('work-calendar.index', [$request->input('workplace_id'), $request->year])->with(compact('alert'));
        }
    }

    /**
     * 4. Guardar horario laborable.
     */
    public function store($data){
        //Comprobamos si la fecha existe:
        $exist = WorkCalendar::select('work_calendars.id', 'work_calendars.day_type_id', 'work_calendars.hours', 'day_types.is_working')
        ->leftJoin('day_types', 'work_calendars.day_type_id', '=', 'day_types.id')
        ->where('work_calendars.workplace_id', $data['workplace_id'])
        ->where('work_calendars.date', $data['date'])
        ->orderBy('work_calendars.id', 'DESC')
        ->first();

        $resp = true;   //Indica si la fecha se ha seleccionado (true) o deseleccionado (false)

        //Insertamos o actualizamos según exista:
        if($exist){
            //Si el día es laborable, se guardan las horas informadas:
            $hours = $exist->is_working? $data['hours']:(!$exist->day_type_id? $data['hours']:NULL);
            
            //Sólo en los días especificados en el array weekdays:
            if(in_array($data['weekDay'], $data['weekdays'])){
                $day_type_id = $data['day_type_id'] && !$exist->is_working? $data['day_type_id']:$exist->day_type_id;

                //Actualizamos:
                WorkCalendar::where('id', $exist->id)
                ->update(['day_type_id' => $day_type_id, 'hours' => $hours]);
            }
        
        //Si no existe:
        }else{
            //Sólo en los días especificados en el array weekdays:
            if(in_array($data['weekDay'], $data['weekdays'])){
                $row = new WorkCalendar;
                $row->workplace_id = $data['workplace_id'];
                $row->day_type_id = $data['day_type_id'];
                $row->date = $data['date'];
                $row->hours = $data['hours'];
                $row->save();   
            }
        }

        return $resp;
    }

    /**
     * 5. Copiar calendario laboral entre empresas y centros de trabajo.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-copy-working-hours');
            return redirect('/admin/work-calendar/'.$request->workplace_id.'/'.$request->year)
                ->withErrors($validator)
                ->withInput();
        }

        //Datos del centro de trabajo a copiar:
        $data = WorkCalendar::where('workplace_id', $request->workplace_id)
        ->whereYear('date', $request->year)
        ->get();

        //Copiar a centros de trabajo:
        if(!empty($request->workplaces)){
            foreach($request->workplaces as $wp){
                //Eliminamos calendario anterior del centro de trabajo destino:
                WorkCalendar::where('workplace_id', $wp)
                ->whereYear('date', $request->year)
                ->delete();

                foreach($data as $row){
                    $dia = WorkCalendar::create([
                        'workplace_id' => $wp,
                        'day_type_id' => $row->day_type_id,
                        'date' => $row->date,
                        'hours' => $row->hours
                    ]);
                }
            }    
        }

        //Copiar a otras empresas:
        if(!empty($request->companies)){
            foreach($request->companies as $c){
                //Centros de trabajo por empresa:
                $workplaces = Workplace::select('id')->where('company_id', $c)->get();

                if($workplaces->count()){
                    foreach($workplaces as $wp){
                        //Eliminamos calendario anterior del centro de trabajo destino:
                        WorkCalendar::where('workplace_id', $wp)
                        ->whereYear('date', $request->year)
                        ->delete();

                        foreach($data as $row){
                            $dia = WorkCalendar::create([
                                'workplace_id' => $wp->id,
                                'day_type_id' => $row->day_type_id,
                                'date' => $row->date,
                                'hours' => $row->hours
                            ]);
                        }
                    } 
                }                
            }
        }

        $msg = trans('textos.calendario_copiado');
        return redirect()->route('work-calendar.index', [$request->input('workplace_id'), $request->year])->with(compact('msg'));
    }

}
