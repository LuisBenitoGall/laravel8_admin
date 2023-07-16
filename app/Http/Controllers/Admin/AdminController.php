<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use File;

//Models:
use App\Models\Company;
use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\DayTypes;
use App\Models\Employee;
use App\Models\Module;
use App\Models\Preference;
use App\Models\QuizEditions;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\User;
use App\Models\UserTimetables;
use App\Models\WorkCalendar;
use App\Models\WorkUserCalendar;

class AdminController extends Controller{
	/**
     * 1. Admin dashboard.
     * 2. Obtener provincias por país.
     * 3. Obtener poblaciones por provincia.
     * 4. Ejecutar sentencias sql.
     */
               
    private $option = 'dashboard';
    private $module = 'dashboard';
   
	/**
     * 1. Admin dashboard.
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request){
    	$header = [
            'option' => $this->option, 
            'suboption' => 'dashboard', 
            'module' => $this->module
        ];

        //Preferencias:
        $preferences = Preference::select('preferences.id', 'preferences.name', 'preferences.url', 'modules.color', 'modules.icon')
        ->leftJoin('modules', 'preferences.module_id', '=', 'modules.id')
        ->where('preferences.user_id', Auth::user()->id)
        ->orderBy('modules.level', 'ASC')
        ->get();

        // $company_emails = CompanySettings::select('emails')
        //     ->where('company_id', session('currentCompany'))
        //     ->first();
            
        // $emailsTo = $company_emails->emails? unserialize($company_emails->emails):false;
    

        //Quizzes activos:
        $quizzes = QuizEditions::select('quiz_editions.id', 'quiz_editions.name', 'quiz_editions.description', 'quiz_editions.start', 'quiz_editions.finish')
        ->join('quiz_edition_companies', 'quiz_editions.id', '=', 'quiz_edition_companies.quiz_edition_id')
        ->where('quiz_editions.status', 1)
        ->where('quiz_edition_companies.company_id', session('currentCompany'))
        ->get();

        return view('admin.dashboard', compact('header', 'preferences', 'quizzes'));
    }

    /**
     * 2. Obtener provincias por país.
     * 
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function getCountryProvinces(Request $request, $id){
        //Validamos la llamada Ajax:
        if($request->ajax()){
            $data = $this->countryProvinces($id, 1);
            return response()->json($data);  
        }
    }

    /**
     * 3. Obtener poblaciones por provincia.
     * 
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function getProvinceTowns(Request $request, $id){
        //Validamos la llamada Ajax:
        if($request->ajax()){
            $data = $this->provinceTowns($id, 1);
            return response()->json($data);  
        }
        return $data;
    }

    /**
     * 4. Ejecutar sentencias sql.
     */
    public function sql(){
        //1. Añadir jornadas de teletrabajo en grupo.
        //$this->teletrabajoColonias();   
        
        //2. Ajuste horarios registros sin conceptos obligatorios.
        $this->adjustRequiredTimetables();
    }

    /**
     * 4.1. Añadir jornadas de teletrabajo en grupo.
     */
    public function teletrabajoColonias(){
        $companies = [4,8,202,227,275,];
        $date = '2023-07-06';

        foreach($companies as $co){
            $users = User::select('users.id')
            ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->where('users.status', 1)
            ->where('user_companies.company_id', $co)
            ->get();   

            $timetable_items = TimetableItems::select('id', 'type')
            ->whereIn('slug', ['inicio-teletrabajo', 'fin-teletrabajo'])
            ->where('company_id', $co)
            ->orderBy('id', 'ASC')
            ->get(); 

            $hours = ['08:00:00', '17:00:00'];

            foreach($users as $u){
                //Comprobamos que no existan registros previos:
                $exist = Timetable::select('id')
                ->where('date', $date)
                ->where('user_id', $u->id)
                ->first();

                $k = 0;

                if(!$exist){
                    foreach($timetable_items as $t){
                        $ti = new Timetable();
                        $ti->date = $date;
                        $ti->hour = $hours[$k];
                        $ti->user_id = $u->id;
                        $ti->item_id = $t->id;
                        $ti->flag = $t->type;
                        $ti->save();

                        $k++;
                    }
                }
            }
        }     
    }


    /**
     * Ajuste horarios registros sin conceptos obligatorios.
     */
    public function adjustRequiredTimetables(){
        $slug = 'timetables';
        $from = strtotime('2023-07-10');
        $to = strtotime('2023-07-15');

        //Obtenemos empresas con control de horarios activo:
        $companies = Company::select('companies.*')
        ->leftJoin('company_modules', 'companies.id', '=', 'company_modules.company_id')
        ->leftJoin('modules', 'company_modules.module_id', '=', 'modules.id')
        ->where('modules.slug', $slug)
        ->where('companies.status', 1)
        ->groupBy('companies.id')
        ->get(); 

        foreach($companies as $co){
            //Conceptos horarios obligatorios para la empresa:
            $required_timetable_items_collection = TimetableItems::where('company_id', $co->id)
            ->whereNotNull('required')
            ->get();

            if($required_timetable_items_collection->count()){
                //Centros de trabajo por empresa:
                $workplaces = $co->workplaces;

                //Concepto de entrada:
                $entrada = TimetableItems::select('id')
                ->where('company_id', $co->id)
                ->where('slug', 'entrada')
                ->where('basic_item', 1)
                ->first();

                foreach($workplaces as $wp){
                    //Empleados por cada centro de trabajo:     
                    $users = Employee::select('users.id', 'employees.workplace_id')
                    ->join('users', 'employees.user_id', '=', 'users.id')
                    ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
                    ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
                    ->where('users.status', 1)
                    ->where('employees.status', 1)
                    ->where('employees.time_control', 1)
                    ->where('employees.workplace_id', $wp->id)
                    ->where('contracts.company_id', $co->id)
                    ->get();

                    //Recorremos todo el intervalo:
                    for($i=$from; $i<=$to; $i+=86400){
                        $date = date('Y-m-d', $i);

                        //Comprobamos si el centro de trabajo tiene festivo:
                        $calendar = WorkCalendar::select('work_calendars.id', 'work_calendars.day_type_id')
                        ->where('work_calendars.workplace_id', $wp->id)
                        ->where('work_calendars.date', $date)
                        ->first();

                        if($calendar){
                            //Verificamos que el tipo de jornada sea laborable.
                            $dayType = false;
                            if($calendar->day_type_id){
                                $day_type = DayTypes::select('is_working', 'subtract_required_concept', 'concepts_not_required')->find($calendar->day_type_id);
                                $dayType = $day_type && $day_type->is_working? true:false;
                            }

                            $isWorkingDay = !$calendar->day_type_id || $dayType? true:false;

                            //Si la jornada es laborable:
                            if($isWorkingDay){
                                //Pasamos items obligatorios a un array y comprobamos por cada centro de trabajo:
                                $required_timetable_items = [];
                                foreach($required_timetable_items_collection as $r){
                                    //Comprobamos si el item tiene alguna condición restrictiva y guardamos el tiempo de duración en minutos, si lo tiene.
                                    if($r->required_condition){
                                        $arr = unserialize($r->required_condition);
                                        $condition = false;

                                        //Aplicando condición:
                                        if(isset($arr['tabla']) &&
                                            isset($arr['campo']) &&    
                                            isset($arr['regla']) &&
                                            isset($arr['valor'])
                                        ){
                                            $condition = DB::table($arr['tabla'])
                                            ->where('workplace_id', $wp->id)
                                            ->where('date', $date)
                                            ->where($arr['campo'], $arr['regla'], $arr['valor'])
                                            ->first();
                                        }    

                                        //Se guarda si cumple la condición:
                                        if($condition){
                                            $required_timetable_items[$r->id] = $r->required;    
                                        }
                                    }else{
                                        $required_timetable_items[$r->id] = $r->required;   
                                    }
                                }

                                //Comprobamos que cada usuario tenga su horario laboral cerrado si no tenía festivo o jornada no presencial:
                                foreach($users as $user){
                                    //Si es jornada festiva del usuario:
                                    $noWork = WorkUserCalendar::noWorkDay($user->id, $date);
                                    //Si no ha tomado festivo o es jornada laborable tipo 'teletrabajo':   && $noWork->in_office
                                    if(!$noWork || ($noWork->is_working && $noWork->subtract_required_concept)){
                                        $items_done = [];

                                        //Conceptos NO obligatorios:
                                        $concepts_not_required = $noWork && $noWork->concepts_not_required? unserialize($noWork->concepts_not_required):[];

                                        //Horarios reducidos del usuario:
                                        $with_custom_hours = false;
                                        $user_timetables = UserTimetables::select('daily_hours')
                                        ->where('user_id', $user->id)
                                        ->where('from', '<=', $date)
                                        ->where('to', '>=', $date)
                                        ->first();
                                        if($user_timetables){
                                            //Día de la semana:
                                            $dia_semana = date('N', strtotime($date));

                                            $user_hours = unserialize($user_timetables->daily_hours);

                                            //Si el número de horas a trabajar por el usuario es inferior al número de horas de empresa, se omite la acción de añadir los conceptos horarios faltantes.
                                            if($user_hours[$dia_semana] > 0 && $calendar->hours > $user_hours[$dia_semana]){
                                                $with_custom_hours = true;
                                            }
                                        }

                                        //Si el usuario no tiene horarios personalizados:
                                        if(!$with_custom_hours){
                                            //Registros de la jornada del usuario:
                                            $user_required_items = Timetable::select('timetables.id', 'timetables.date', 'timetables.hour', 'timetables.item_id', 'timetables.flag', 'timetable_items.basic_item')
                                            ->join('timetable_items', 'timetables.item_id', '=', 'timetable_items.id')
                                            ->where('timetables.user_id', $user->id)
                                            ->where('date', $date)
                                            ->orderBy('timetables.id', 'ASC')
                                            ->get();

                                            //El primer registro debe ser entrada ordinaria, pues sólo incluye a los conceptos por trabajo en oficina:
                                            if($user_required_items->count() && $user_required_items[0]->basic_item){
                                                foreach($user_required_items as $uri){
                                                    //Los items requeridos existentes se guardan en nuevo array:
                                                    if(array_key_exists($uri->item_id, $required_timetable_items)){
                                                        array_push($items_done, $uri->item_id);
                                                    }
                                                }

                                                //Insert de los registros obligatorios no hallados:
                                                foreach($required_timetable_items as $idx => $rti){
                                                    //Si el índice no está como valor en el array de items cumplidos:
                                                    if(!in_array($idx, $items_done) && !in_array($idx, $concepts_not_required)){
                                                        //Hora de inicio del registro:
                                                        $hour_from = '10:00:00';
                                                        foreach($required_timetable_items_collection as $r_){
                                                            if($r_->id == $idx){
                                                                $hour_from = $r_->required_warning;
                                                                break;
                                                            }
                                                        }

                                                        //Registro de salida:
                                                        $t_out = new Timetable();
                                                        $t_out->date = $date;
                                                        $t_out->hour = $hour_from.':00:00';
                                                        $t_out->user_id = $user->id;
                                                        $t_out->item_id = $idx;
                                                        $t_out->flag = 2;
                                                        $t_out->save();

                                                        $minutes = helpers::getMinutes($t_out->hour) + $rti;
                                                        $hora_entrada = date('H:i:s', mktime(0,$minutes,0));

                                                        //Registro de entrada:
                                                        $t_in = new Timetable();
                                                        $t_in->date = $date;
                                                        $t_in->hour = $hora_entrada;
                                                        $t_in->user_id = $user->id;
                                                        $t_in->item_id = $entrada->id;
                                                        $t_in->flag = 1;
                                                        $t_in->save();
                                                    }    
                                                } 
                                            }
                                        }
                                    }     
                                }
                            }  
                        }              
                    }
                }
            }
        }   
    }

    public function testRequiredTimetableItemsTask(){
        $slug = 'timetables';
        $date = '2023-07-10';

        //Obtenemos empresas con control de horarios activo:
        $companies = Company::select('companies.*')
        ->leftJoin('company_modules', 'companies.id', '=', 'company_modules.company_id')
        ->leftJoin('modules', 'company_modules.module_id', '=', 'modules.id')
        ->where('modules.slug', $slug)
        ->where('companies.status', 1)
        ->get();

        foreach($companies as $co){
            //Conceptos horarios obligatorios para la empresa:
            $required_timetable_items_collection = TimetableItems::where('company_id', $co->id)
            ->whereNotNull('required')
            ->get();

            if($required_timetable_items_collection->count()){
                //Centros de trabajo por empresa:
                $workplaces = $co->workplaces;

                //Concepto de entrada:
                $entrada = TimetableItems::select('id')
                ->where('company_id', $co->id)
                ->where('slug', 'entrada')
                ->where('basic_item', 1)
                ->first();

                foreach($workplaces as $wp){
                    //Comprobamos si el centro de trabajo tiene festivo:
                    $calendar = WorkCalendar::select('work_calendars.id', 'work_calendars.hours', 'work_calendars.day_type_id', 'day_types.is_working')
                    ->leftJoin('day_types', 'work_calendars.day_type_id', '=', 'day_types.id')
                    ->where('work_calendars.workplace_id', $wp->id)
                    ->where('work_calendars.date', $date)
                    ->first();

                    //Si la jornada es laborable (que esté declarada como tal o no tenga declarado day_type_id):
                    //&& ($calendar->is_working || $calendar->hours > 0)
                    if($calendar){
                        //Verificamos que el tipo de jornada sea laborable.
                        $dayType = false;
                        if($calendar->day_type_id){
                            $day_type = DayTypes::select('is_working', 'subtract_required_concept', 'concepts_not_required')->find($calendar->day_type_id);
                            $dayType = $day_type && $day_type->is_working? true:false;
                        }

                        $isWorkingDay = !$calendar->day_type_id || $dayType? true:false;

                        //Si la jornada es laborable:
                        if($isWorkingDay){
                            //Pasamos items obligatorios a un array y comprobamos por cada centro de trabajo:
                            $required_timetable_items = [];
                            foreach($required_timetable_items_collection as $r){
                                //Comprobamos si el item tiene alguna condición restrictiva:
                                if($r->required_condition){
                                    $arr = unserialize($r->required_condition);
                                    $condition = false;

                                    //Aplicando condición:
                                    if(isset($arr['tabla']) &&
                                        isset($arr['campo']) &&    
                                        isset($arr['regla']) &&
                                        isset($arr['valor'])
                                    ){
                                        $condition = DB::table($arr['tabla'])
                                        ->where('workplace_id', $wp->id)
                                        ->where('date', $date)
                                        ->where($arr['campo'], $arr['regla'], $arr['valor'])
                                        ->first();
                                    }    

                                    //Se guarda si cumple la condición:
                                    if($condition){
                                        $required_timetable_items[$r->id] = $r->required;    
                                    }
                                }else{
                                    $required_timetable_items[$r->id] = $r->required;   
                                }
                            }

                            //Empleados por cada centro de trabajo:     
                            $users = Employee::select('users.id', 'employees.workplace_id')
                            ->join('users', 'employees.user_id', '=', 'users.id')
                            ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
                            ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
                            ->where('users.status', 1)
                            ->where('employees.status', 1)
                            ->where('employees.time_control', 1)
                            ->where('employees.workplace_id', $wp->id)
                            ->where('contracts.company_id', $co->id)
                            ->groupBy('users.id')
                            ->get();

                            //Comprobamos que cada usuario tenga su horario laboral cerrado si no tenía festivo o jornada no presencial:
                            foreach($users as $user){
                                //Si es jornada festiva del usuario:
                                $noWork = WorkUserCalendar::noWorkDay($user->id, $date);

                                if(!$noWork || ($noWork->is_working && $noWork->subtract_required_concept)){
                                    //Array donde guardamos los items ya revisados:
                                    $items_done = [];

                                    //Conceptos NO obligatorios:
                                    $concepts_not_required = $noWork && $noWork->concepts_not_required? unserialize($noWork->concepts_not_required):[];

                                    //Horarios reducidos del usuario:
                                    $with_custom_hours = false;
                                    $user_timetables = UserTimetables::select('daily_hours')
                                    ->where('user_id', $user->id)
                                    ->where('from', '<=', $date)
                                    ->where('to', '>=', $date)
                                    ->first();
                                    if($user_timetables){
                                        //Día de la semana:
                                        $dia_semana = date('N', strtotime($date));

                                        $user_hours = unserialize($user_timetables->daily_hours);

                                        //Si el número de horas a trabajar por el usuario es inferior al número de horas de empresa, se omite la acción de añadir los conceptos horarios faltantes.
                                        if($user_hours[$dia_semana] > 0 && $calendar->hours > $user_hours[$dia_semana]){
                                            $with_custom_hours = true;
                                        }
                                    }

                                    //Si el usuario no tiene horarios personalizados:
                                    if(!$with_custom_hours){
                                        //Registros de la jornada del usuario:
                                        $user_required_items = Timetable::select('timetables.id', 'timetables.date', 'timetables.hour', 'timetables.item_id', 'timetables.flag', 'timetable_items.basic_item')
                                        ->join('timetable_items', 'timetables.item_id', '=', 'timetable_items.id')
                                        ->where('timetables.user_id', $user->id)
                                        ->where('date', $date)
                                        ->orderBy('timetables.id', 'ASC')
                                        ->get();

                                        //dd($user->id, $required_timetable_items, $concepts_not_required);

                                        //El primer registro debe ser entrada ordinaria, pues sólo incluye a los conceptos por trabajo en oficina:
                                        if($user_required_items->count() && $user_required_items[0]->basic_item){
                                            foreach($user_required_items as $uri){
                                                //Los items requeridos existentes se guardan en nuevo array:
                                                if(array_key_exists($uri->item_id, $required_timetable_items)){
                                                    array_push($items_done, $uri->item_id);
                                                }
                                            }

                                            //Insert de los registros obligatorios no hallados:
                                            foreach($required_timetable_items as $idx => $rti){
                                                //Si el índice no está como valor en el array de items cumplidos + el tipo de jornada no tiene exento este concepto horario:
                                                if(!in_array($idx, $items_done) && !in_array($idx, $concepts_not_required)){
                                                    //Hora de inicio del registro:
                                                    $hour_from = '10:00:00';
                                                    foreach($required_timetable_items_collection as $r_){
                                                        if($r_->id == $idx){
                                                            $hour_from = $r_->required_warning;
                                                            break;
                                                        }
                                                    }

                                                    //Registro de salida:
                                                    $t_out = new Timetable();
                                                    $t_out->date = $date;
                                                    $t_out->hour = $hour_from.':00:00';
                                                    $t_out->user_id = $user->id;
                                                    $t_out->item_id = $idx;
                                                    $t_out->flag = 2;
                                                    $t_out->save();

                                                    $minutes = helpers::getMinutes($t_out->hour) + $rti;
                                                    $hora_entrada = date('H:i:s', mktime(0,$minutes,0));

                                                    //Registro de entrada:
                                                    $t_in = new Timetable();
                                                    $t_in->date = $date;
                                                    $t_in->hour = $hora_entrada;
                                                    $t_in->user_id = $user->id;
                                                    $t_in->item_id = $entrada->id;
                                                    $t_in->flag = 1;
                                                    $t_in->save();
                                                }    
                                            } 
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }    
    }
}
