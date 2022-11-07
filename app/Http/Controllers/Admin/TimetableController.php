<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use PDF;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\WorkCalendar;
use App\Models\Workplace;
use App\Models\WorkUserCalendar;

class TimetableController extends Controller{
    /**
     * 1. Listado de horarios por empleados.
     * 1.1. Listado de horarios por empleados para DataTable.
     * 2. Guardar fichaje horario de usuario.
     * 3. Editar horario.
     * 4. Actualizar horario.
     * 5. Eliminar registro horario.
     * 6. Marcar horario para revisar.
     * 7. Desmarcar horario revisado.
     * 8. Enviar email revisión.
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
        //Validación save intervals:
        if(isset($data['submit_filter'])){
            return Validator::make($data, [
                'workplace_id' => 'required|integer'
            ]); 

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'item_id' => 'required|integer',
                'hora' => 'required|numeric|max:23',
                'minutos' => 'required|numeric|max:59',
                'segundos' => 'required|numeric|max:59'
            ],[
                'hora.required' => trans('textos.hora_obligatoria'),
                'minutos.required' => trans('textos.hora_obligatoria'),
                'segundos.required' => trans('textos.hora_obligatoria')
            ]);
        }
    } 

    /**
     * 1. Listado de horarios por empleados.
     */
    public function index(Request $request){
        if($request->all()){
            $validator = $this->validator($request->all());
            if($validator->fails()){
                return redirect('/admin/employees-timetable')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.horarios_empleados',
            'module' => $this->module,
            'slug' => 'employees-timetable'
        ]; 

        $workplace = $request->workplace_id? Workplace::find($request->workplace_id):false;
        $from = $request->from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:Date('Y-m-d');
        $to = $request->to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:Date('Y-m-d');

        //La fecha de finalización no puede ser inferior a la de inicio:
        if($to < $from) $to = $from;

        //Centros de trabajo:
        $workplaces = Workplace::select('workplaces.id', 'workplaces.name', 'towns.name AS Town')
        ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
        ->where('workplaces.company_id', session('currentCompany'))
        ->where('workplaces.status', 1)
        ->orderBy('workplaces.name', 'ASC')
        ->get();

        $languages = $this->languages;

        return view('admin.timetables.index')->with(compact('header', 'workplace', 'from', 'to', 'workplaces', 'languages'));
    }

    /**
     * 1.1. Listado de horarios por empleados para DataTable.
     */
    public function getEmployeesTimetablesDT($workplace_id = false, $from = false, $to = false){
        if(!$workplace_id || !$from || !$to){
            return false;
            exit;
        }
        
        $data = User::select('users.id', 'users.name', 'users.surname')
        ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
        ->leftJoin('user_workplaces', 'users.id', '=', 'user_workplaces.user_id')
        ->where('employees.status', 1)
        ->where('employees.time_control', 1)
        ->where('user_workplaces.workplace_id', $workplace_id)
        ->orderBy('users.name')
        ->groupBy('users.id')
        ->get();   

        $from_default = $from; 

        //Ampliación datos:
        foreach($data as $row){
            $timetables = Timetable::listUserTimetables($from, $to, $row->id, $workplace_id);

            //Totales:
            $worked_seconds = 0;
            $working_seconds = 0;

            if(count($timetables) > 0){
                foreach($timetables as $tm){
                    if($tm['accumulated']){
                        $worked_seconds = $worked_seconds + helpers::getSeconds($tm['accumulated']);    
                    } 

                    if($tm['hoursOfWork']){
                        $working_seconds = $working_seconds + helpers::getSeconds($tm['hoursOfWork']);
                    }
                }    
            }

            //Nuevos parámetros:
            $row->worked_hours = $worked_seconds > 0? helpers::secondsToHoursMinutes($worked_seconds):'';
            $row->working_hours = $working_seconds > 0? helpers::secondsToHoursMinutes($working_seconds):'';

            $diff = $worked_seconds - $working_seconds;
            $row->diferencia = $diff != '0'? helpers::secondsToHoursMinutes(abs($diff)):'00:00';
            $row->sign = $diff < 0? '-':'+';

            //Situación del usuario, en función de su último registro horario:
            $last_register = Timetable::select('flag')
            ->where('user_id', $row->id)
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
            $row->last_register = $last_register? $last_register->flag:2;

            //Reseteamos fecha from a su valor original:
            $from = $from_default;
        }

        return DataTables::of($data)
        ->addColumn('employee', function($row){
            return $row->name.' '.$row->surname;
        })
        ->addColumn('difference', function($row){
            $difference = '';
            if($row->diferencia && $row->worked_hours != ''){
                $clase = $row->sign == '-'? 'text-danger':'text-success';
                $difference = ' <strong class="'.$clase.'">';
                if($row->sign == '-'){
                    $difference .= '-';
                }
                $difference .= $row->diferencia.'</strong>';
            }   
            return $difference; 
        })
        ->addColumn('situation', function($row){
            if($row->last_register){
                if($row->last_register == 1){
                    return '<i class="fa fa-sign-in-alt text-success" rel="tooltip" title="'.ucfirst(trans('textos.dentro')).'"></i>'; 
                }else if($row->last_register == 2){
                    return '<i class="fa fa-sign-out-alt text-danger" rel="tooltip" title="'.ucfirst(trans('textos.fuera')).'"></i>';
                }                   
            }else{
                return '';
            }     
        })
        ->addColumn('acciones', function($row){
            return view('admin.timetables.partials.dt-actions', compact('row'));
        })
        ->rawColumns(['difference', 'situation'])
        ->make(true);
    }

    /**
     * 2. Guardar fichaje horario de usuario.
     */
    public function store(TimetableItems $item){
        if($item && is_numeric($item->id)){
            //El concepto horario debe pertenecer a la empresa en sesión:
            if($item->company_id != session('currentCompany')){
                $alert = trans('textos.no_permisos_accion');
                return redirect()->route('dashboard')->with(compact('alert'));
                exit;
            }

            $dia = date('Y-m-d');
            //Formato hora:
            $hora = date("H:i");
            $revisable = $item->reviewable? 1:0;
        
            //Obtenemos último registro del usuario:
            $prevRecord = Timetable::select('timetables.id', 'timetables.date', 'timetables.hour', 'timetables.item_id', 'timetables.flag', 'timetable_items.min', 'timetable_items.max', 'timetable_items.fixed')
            ->join('timetable_items', 'timetables.item_id', '=', 'timetable_items.id')
            ->where('timetables.user_id', Auth::user()->id)
            ->orderBy('timetables.date', 'DESC')
            ->orderBy('timetables.hour', 'DESC')
            ->first();

            //Evitar repetir flag:
            if($prevRecord && $prevRecord->flag == $item->type){
                return back();
                exit;
            }

            //Comprobaciones registros anteriores:
            if($prevRecord){
                //La hora actual no puede ser anterior a la última hora registrada en el mismo día:
                if($prevRecord->date == Carbon::today() && date('H:i:s', time()) < $prevRecord->hour){
                    return back();
                    exit;    
                }

                //Evitar duplicados:
                $duplicates = Timetable::select('id')
                ->where('date', $prevRecord->date)
                ->where('hour', $prevRecord->hour)
                ->where('user_id', Auth::user()->id)
                ->where('item_id', $prevRecord->item_id)
                ->where('flag', $prevRecord->flag)
                ->get();

                if($duplicates->count() > 1){
                    foreach($duplicates as $d){
                        if($d->id != $prevRecord->id){
                            //Borrado definitivo:
                            $d->forceDelete();
                        }
                    }
                }

                //El nuevo registro horario no puede coincidir con el anterior, por lo que se suma 1 minuto en caso de coincidencia:
                if($hora.':00' == $prevRecord->hour){
                    $add_minute = strtotime('+1 minute', strtotime($hora));
                    $hora = date('H:i', $add_minute);
                }

                //Si tiene un período mínimo y el tiempo transcurrido entre el anterior registro y el momento actual es inferior a ese mínimo, la hora de entrada es la suma de la hora: 
                if($prevRecord->min > 0 && helpers::getMinutes(helpers::getTimeBetweenHours($prevRecord->hour, $hora)) < $prevRecord->min){
                    $hora = helpers::sumaHoras($prevRecord->hour, helpers::getTimeFromMinutes($prevRecord->min));
                }
            }

            //Guardamos registro horario:
            $hour = new Timetable();
            $hour->date = $dia;
            $hour->hour = $hora;
            $hour->user_id = Auth::user()->id;
            $hour->item_id = $item->id;
            $hour->flag = $item->type;
            $hour->revision = $revisable;
            $hour->save();

            return redirect()->route('users.timetables', Auth::user()->id);

        }else{
            return redirect('/');
        }   
    }

    /**
     * 2.1. Guardar jornada laboral de empleado.
     */
    public function add(Request $request){
        if($request->date){
            //Entrada:
            $hora_in = $request->input('hora_in').':'.$request->input('minutos_in').':'.$request->input('segundos_in');
            $hour_in = new Timetable();
            $hour_in->date = $request->date;
            $hour_in->hour = $hora_in;
            $hour_in->user_id = $request->user_id;
            $hour_in->item_id = $request->item_id_in;
            $hour_in->flag = 1;
            $hour_in->revision = 0;
            $hour_in->comments = $request->comments;
            $hour_in->save();

            //Salida:
            $hora_out = $request->input('hora_out').':'.$request->input('minutos_out').':'.$request->input('segundos_out');
            $hour_out = new Timetable();
            $hour_out->date = $request->date;
            $hour_out->hour = $hora_out;
            $hour_out->user_id = $request->user_id;
            $hour_out->item_id = $request->item_id_out;
            $hour_out->flag = 2;
            $hour_out->revision = 0;
            $hour_out->save();

            //Eliminamos posibles registros en work_user_calendars, pues si es fecha trabajada, no puede ser una fecha disfrutada por el usuario:
            WorkUserCalendar::deleteRowsByUserDate($request->user_id, $request->date);
    
            $msg = trans('textos.registro_horario_updated');
            return redirect()->route('users.timetables', [$request->user_id, $request->date])->with(compact('msg')); 
        }   
    }

    /**
     * 3. Editar horario de usuario.
     */
    public function edit(Request $request, Timetable $timetable){
        if($request->ajax()){
            return response()->json($timetable);
        }
    }

    /**
     * 4. Actualizar horario.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-timetable');
            return redirect('/admin/users/'.$request->user_id.'/timetables')
                ->withErrors($validator)
                ->withInput();
        }

        $hour = $request->hora.':'.$request->minutos.':'.$request->segundos;

        $timetable = Timetable::find($request->id);
        $timetable->hour = $hour;
        $timetable->item_id = $request->item_id;
        $timetable->comments = $request->comments;
        $timetable->save();

        $msg = trans('textos.registro_horario_updated');
        return redirect()->route('users.timetables', [$timetable->user_id, $timetable->date])->with(compact('msg'));
    }

    /**
     * 5. Eliminar registro horario.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timetable $timetable){
        $user_id = $timetable->user_id;
        $add_register = $timetable->date;

        $timetable->delete();  

        $msg = trans('textos.registro_horario_deleted');
        return redirect()->route('users.timetables', [$user_id, $add_register])->with(compact('msg'));   
    }

    /**
     * 6. Marcar horario para revisar.
     */
    public function revisionHorarioOn(Request $request){
        if($request->ajax()){
            //Actualizamos registro horario:
            $hour = Timetable::find($request->input('id'));
            $hour->revision = 1;
            $hour->comments = $hour->comments.' '.$request->input('comments');
            $hour->save();    

            $user = User::find($hour->user_id);

            //Enviamos email:
            $this->mailRevision($user, $hour, $request->input('comments'));

            $resp = trans('textos.solicitud_revision_guardada');
            return response()->json($resp);  
        }
    }

    /**
     * 7. Desmarcar horario revisado.
     */
    public function revisionHorarioOff(Request $request, $id = false){
        if($request->ajax() && $id){
            //Actualizamos registro horario:
            $hour = Timetable::find($id);
            $hour->revision = 0;
            $hour->save();  

            $resp = ($hour)? true:false;
            return response()->json($resp);       
        }
    }

    /**
     * 8. Enviar email revisión.
     */
    public function mailRevision($user, $hour, $comments, $concepto = false){
        //Enviamos email:
        $data = [
            'comments' => $comments,
            'nombre' => $user->name,
            'apellidos' => $user->surname,
            'fecha' => $hour->date,
            'hora' => $hour->hour,
            'concepto' => $concepto
        ];
        $company = Company::find(session('currentCompany'));
        $data['company'] = $company;
        $emailFrom = $user->email;
        $settings = CompanySettings::where('company_id', session('currentCompany'))->first();
        $destinatarios = $settings->emails? unserialize($settings->emails):false;

        if($destinatarios){
            foreach($destinatarios as $emailTo){
                Mail::send('emails.send-timetable-revision', $data, function($message) use($emailFrom, $company, $emailTo){
                    $message->from($emailFrom);
                    $message->to($emailTo);
                    $message->subject(ucfirst(trans('textos.solicitar_revision'))); 
                }); 
            }  
        }    
    }

    /**
     * Ajuste horarios de empleados.
     *
     * Revisar los horarios de todos los empleados desde el 01/09/21 e incluir la hora de comida para todos aquellos que en jornadas laborales completas no lo tenga contemplado.
     */
    public function adjustTimetables(){
        $adn_companies = [3,4,8,202,227,275];
        $from = '2021-09-01';
        $to = '2022-08-31';
        $hour_out = '14:00:00';
        $hour_in = '15:00:00';
        $top_minutes = 470;     //Marca el límite por debajo de 8 horas, por debajo del cual no se marca hora de comida.
        $summer_days = ['start' => '2022-07-11', 'end' => '2022-08-31'];    //Intervalo de fechas de jornada intensiva.

        $timetable_items = [
            //3 => ['comida' => '', 'entrada' => ''],
            4 => ['comida' => 5, 'entrada' => 1],
            8 => ['comida' => 18, 'entrada' => 14],
            202 => ['comida' => 31, 'entrada' => 27],
            227 => ['comida' => 44, 'entrada' => 40],
            275 => ['comida' => 57, 'entrada' => 53]
        ];

        $teletrabajo = [
            4 => [ 9, 10],
            8 => [ 22, 23],
            202 => [ 35, 36],
            227 => [ 48, 49],
            275 => [ 61, 62]    
        ];

        //Nº de horas a trabajar en cada jornada del intervalo por cada centro de trabajo:
        $working_hours = WorkCalendar::select('id', 'workplace_id', 'date', 'hours')
        ->whereNotNull('hours')
        ->where('date', '>=', $from)
        ->where('date', '<=', $to)
        ->orderBy('date', 'ASC')
        ->get();

        //Creamos array tomando workplace_id y date como índice:
        $arrWorkingHours = [];
        foreach($working_hours as $wh){
            if(!isset($arrWorkingHours[$wh->workplace_id])){
                $arrWorkingHours[$wh->workplace_id] = [];    
            }

            //Descontamos las fechas de jornadas intensivas estivales:
            if($wh->date < $summer_days['start'] || $wh->date > $summer_days['end']){
                $minutes = helpers::getMinutes($wh->hours);
                if(!array_key_exists($wh->date, $arrWorkingHours[$wh->workplace_id]) && $minutes > $top_minutes){
                    array_push($arrWorkingHours[$wh->workplace_id], $wh->date);    
                }
            }
        }

        $users = Employee::select('employees.user_id', 'employees.workplace_id', 'workplaces.company_id')
        ->join('workplaces', 'employees.workplace_id', '=', 'workplaces.id')
        ->whereIn('workplaces.company_id', $adn_companies)
        ->groupBy('employees.user_id')
        ->get();

        foreach($users as $u){
            //Buscamos los items horarios registrados por cada jornada:
            foreach($arrWorkingHours[$u->workplace_id] as $f){
                //Descartes por teletrabajo:
                $arrTeletrabajo = $teletrabajo[$u->company_id];

                //Horarios del usuario:
                $horarios = Timetable::select('date', 'hour', 'item_id')
                ->where('user_id', $u->user_id)
                ->where('date', $f)
                ->whereNotIn('item_id', $arrTeletrabajo)
                ->get();  

                //Comprobamos que exista algún registro por hora de comida
                if($horarios->count()){
                    $dinner = false; 
                    foreach($horarios as $h){
                        if($timetable_items[$u->company_id]['comida'] == $h->item_id){
                            $dinner = true;
                            break;
                        }
                    } 

                    //Si no se ha encontrado, se generan los registros:
                    if(!$dinner){
                        //Salida a comer:
                        $t = new Timetable();
                        $t->date = $f;
                        $t->hour = $hour_out;
                        $t->user_id = $u->user_id;
                        $t->item_id = $timetable_items[$u->company_id]['comida'];
                        $t->flag = 2;
                        $t->save();

                        //Entrada:
                        $t_ = new Timetable();
                        $t_->date = $f;
                        $t_->hour = $hour_in;
                        $t_->user_id = $u->user_id;
                        $t_->item_id = $timetable_items[$u->company_id]['entrada'];
                        $t_->flag = 1;
                        $t_->save();
                    } 
                } 
            }
        }
    }
}
