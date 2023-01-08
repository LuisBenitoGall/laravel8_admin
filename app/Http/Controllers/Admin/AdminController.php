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
use App\Models\Employee;
use App\Models\Module;
use App\Models\Preference;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\WorkCalendar;
use App\Models\WorkUserCalendar;

class AdminController extends Controller{
	/**
     * 1. Admin dashboard.
     * 2. Obtener provincias por país.
     * 3. Obtener poblaciones por provincia.
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

        $preferences = Preference::select('preferences.id', 'preferences.name', 'preferences.url', 'modules.color', 'modules.icon')
        ->leftJoin('modules', 'preferences.module_id', '=', 'modules.id')
        ->where('preferences.user_id', Auth::user()->id)
        ->orderBy('modules.level', 'ASC')
        ->get();

        // $company_emails = CompanySettings::select('emails')
        //     ->where('company_id', session('currentCompany'))
        //     ->first();
            
        // $emailsTo = $company_emails->emails? unserialize($company_emails->emails):false;

        // dd($emailsTo);

        $this->adjustRequiredTimetables();


        return view('admin.dashboard', compact('header', 'preferences'));
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

    public function test(){
        $slug = 'timetables';

        //Obtenemos empresas con control de horarios activo:
        $companies = CompanyModules::select('companies.*')
        ->join('companies', 'company_modules.company_id', '=', 'companies.id')
        ->join('modules', 'company_modules.module_id', '=', 'modules.id')
        ->where('modules.slug', $slug)
        ->where('companies.status', 1)
        ->get();

        foreach($companies as $co){
            //Conceptos horarios obligatorios para la empresa:
            $required_timetable_items_collection = TimetableItems::where('company_id', $co->id)
            ->whereNotNull('required')
            ->get();

            if($required_timetable_items_collection->count()){
                //Pasamos items a un array:
                $required_timetable_items = [];
                foreach($required_timetable_items_collection as $r){
                    $required_timetable_items[$r->id] = $r->required;   
                }

                //Empleados por cada empresa:     
                $users = Employee::select('users.id')
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
                ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
                ->where('users.status', 1)
                ->where('employees.status', 1)
                ->where('employees.time_control', 1)
                ->where('user_companies.company_id', $co->id)
                ->where('contracts.company_id', $co->id)
                ->get();

                //Concepto de entrada:
                $entrada = TimetableItems::select('id')
                ->where('company_id', $co->id)
                ->where('slug', 'entrada')
                ->where('basic_item', 1)
                ->first();

                //Comprobamos que cada usuario tenga su horario laboral cerrado si no tenía festivo o jornada no presencial:
                foreach($users as $user){
                    //Si es jornada festiva:
                    $noWork = WorkUserCalendar::noWorkDay($user->id, Carbon::today());

                    //Si no ha tomado festivo o es jornada laborable tipo 'teletrabajo':
                    if(!$noWork || ($noWork->is_working && $noWork->in_office)){
                        $items_done = [];

                        //Registros de la jornada del usuario:
                        $user_required_items = Timetable::where('user_id', $user->id)
                        ->where('date', Carbon::today())
                        ->orderBy('id', 'ASC')
                        ->get();

                        foreach($user_required_items as $uri){
                            //Guardamos en array los items requeridos ya cumplidos:
                            if(array_key_exists($uri->item_id, $required_timetable_items)){
                                array_push($items_done, $uri->item_id);
                            }
                        }
                    

                        //Insert de los registros obligatorios no hallados:
                        foreach($required_timetable_items as $idx => $rti){
                            //Si el índice no está como valor en el array de items cumplidos:
                            if(!in_array($idx, $items_done)){
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
                                $t_out->date = Carbon::today();
                                $t_out->hour = $hour_from;
                                $t_out->user_id = $user->id;
                                $t_out->item_id = $idx;
                                $t_out->flag = 2;
                                $t_out->save();

                                $minutes = helpers::getMinutes($t_out->hour) + $rti;
                                $hora_entrada = date('H:i:s', mktime(0,$minutes,0));

                                //Registro de entrada:
                                $t_in = new Timetable();
                                $t_in->date = Carbon::today();
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

    public function test1(){
        $horaCierre = '17:00:00';
        $msgCierre = trans('textos.horario_cerrado_automaticamente');
        $slug = 'timetables';

        //Obtenemos empresas con control de horarios activo:
        $companies = CompanyModules::select('companies.*')
        ->join('companies', 'company_modules.company_id', '=', 'companies.id')
        ->join('modules', 'company_modules.module_id', '=', 'modules.id')
        ->where('modules.slug', $slug)
        ->where('companies.status', 1)
        ->get();

        foreach($companies as $co){
            //Emails de la empresa:
            $company_emails = CompanySettings::select('emails')
            ->where('company_id', $co->id)
            ->first();

           if($company_emails){
                $emailsTo = $company_emails->emails? unserialize($company_emails->emails):false;

                //Empleados por cada empresa:
                $users = Employee::select('users.id', 'users.name', 'users.surname', 'users.email')
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
                ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
                ->where('users.status', 1)
                ->where('employees.status', 1)
                ->where('employees.time_control', 1)
                ->where('user_companies.company_id', $co->id)
                ->where('contracts.company_id', $co->id)
                ->get();

                //Concepto de salida:
                $salida = TimetableItems::select('id')
                ->where('company_id', $co->id)
                ->where('slug', 'salida')
                ->where('basic_item', 1)
                ->first();

                //Comprobamos que cada usuario tenga su horario laboral cerrado si no tenía festivo:
                foreach($users as $user){
                    $noWork = WorkUserCalendar::noWorkDay($user->id, Carbon::today());

                    //Si no ha tomado festivo o es jornada laborable tipo 'teletrabajo':
                    if(!$noWork || ($noWork->is_working && $noWork->working_day)){
                        $horario = Timetable::select('id', 'hour', 'item_id', 'flag', 'date')
                        ->where('user_id', $user->id)
                        ->whereNull('deleted_at')
                        ->orderBy('date', 'DESC')
                        ->orderBy('hour', 'DESC')
                        ->orderBy('flag', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->first();


                        //Si el horario no está cerrado:
                        if($horario && $horario->flag == 1){
                            $hour = new Timetable();
                            $hour->date = Carbon::today();
                            $hour->hour = $horaCierre;
                            $hour->user_id = $user->id;
                            $hour->item_id = $salida->id;
                            $hour->flag = 2;
                            $hour->comments = $msgCierre;
                            $hour->save();

                            //Enviamos email al usuario:
                            $data_user = [
                                'user' => $user,
                                'fecha' => date('d-m-Y'),
                                'company' => $co
                            ];

                            Mail::send('emails.send-timetable-unclosed-user', $data_user, function($message) use($user, $co){
                                $message->from(config('constants.EMAIL_'), $co->name);
                                $message->to($user->email);
                                $message->subject(trans('textos.horario_cerrado_automaticamente'));
                            });
                        }
                    }
                }

                //Recuperamos todos los cierres automáticos del día y se envían por email a los responsables:
                $cierres = Timetable::select('timetables.hour', 'users.name', 'users.surname')
                ->join('users', 'timetables.user_id', '=', 'users.id')
                ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
                ->where('timetables.comments', $msgCierre)
                ->where('timetables.flag', 2)
                ->where('user_companies.company_id', $co->id)
                ->whereDate('timetables.date', Carbon::today())
                ->get();

                if($cierres->count()){
                    $data = [
                        'cierres' => $cierres,
                        'fecha' => date('d-m-Y'),
                        'company' => $co
                    ];

                    Mail::send('emails.send-timetable-unclosed-list', $data, function($message) use($emailsTo, $co){
                        $message->from(config('constants.EMAIL_'), $co->name);
                        $message->to($emailsTo);
                        $message->subject(trans('textos.horarios_cerrados_automaticamente_lista'));
                    });
                }
            }
        }
    }

    /**
     * Ajuste horarios registros sin conceptos obligatorios.
     */
    public function adjustRequiredTimetables(){
        $slug = 'timetables';
        $from = strtotime('2022-12-01');
        $to = strtotime('2023-01-03');

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
                        $fecha = date('Y-m-d', $i);

                        //Comprobamos si el centro de trabajo tiene festivo:
                        $calendar = WorkCalendar::select('work_calendars.id', 'day_types.is_working')
                        ->leftJoin('day_types', 'work_calendars.day_type_id', '=', 'day_types.id')
                        ->where('work_calendars.workplace_id', $wp->id)
                        ->where('work_calendars.date', $fecha)
                        ->first();

                        //dd($fecha, $calendar);

                        //Si la jornada es laborable:
                        if($calendar && $calendar->is_working){
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
                                        ->where('date', $fecha)
                                        ->where($arr['campo'], $arr['regla'], $arr['valor'])
                                        ->first();
                                    }    

                                    //dd($condition);

                                    //Se guarda si cumple la condición:
                                    if($condition){
                                        $required_timetable_items[$r->id] = $r->required;    
                                    }
                                }else{
                                    $required_timetable_items[$r->id] = $r->required;   
                                }
                            }

                            // if($fecha == '2022-12-02'){
                            //     dd($fecha, $required_timetable_items);
                            // }

                            //Comprobamos que cada usuario tenga su horario laboral cerrado si no tenía festivo o jornada no presencial:
                            foreach($users as $user){
                                //Si es jornada festiva del usuario:
                                $noWork = WorkUserCalendar::noWorkDay($user->id, $fecha);

                                //Si no ha tomado festivo o es jornada laborable tipo 'teletrabajo':
                                if(!$noWork || ($noWork->is_working && $noWork->in_office)){
                                    $items_done = [];

                                    //Registros de la jornada del usuario:
                                    $user_required_items = Timetable::where('user_id', $user->id)
                                    ->where('date', $fecha)
                                    ->orderBy('id', 'ASC')
                                    ->get();

                                    foreach($user_required_items as $uri){
                                        //Los items requeridos existentes se guardan en nuevo array:
                                        if(array_key_exists($uri->item_id, $required_timetable_items)){
                                            array_push($items_done, $uri->item_id);
                                        }
                                    }

                                    //Insert de los registros obligatorios no hallados:
                                    foreach($required_timetable_items as $idx => $rti){
                                        //Si el índice no está como valor en el array de items cumplidos:
                                        if(!in_array($idx, $items_done)){
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
                                            $t_out->date = $fecha;
                                            $t_out->hour = $hour_from.':00:00';
                                            $t_out->user_id = $user->id;
                                            $t_out->item_id = $idx;
                                            $t_out->flag = 2;
                                            $t_out->save();

                                            $minutes = helpers::getMinutes($t_out->hour) + $rti;
                                            $hora_entrada = date('H:i:s', mktime(0,$minutes,0));

                                            //Registro de entrada:
                                            $t_in = new Timetable();
                                            $t_in->date = $fecha;
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
