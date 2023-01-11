<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Helpers\helpers;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\WorkCalendar;
use App\Models\WorkUserCalendar;

class RequiredTimetableItemsTask extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'required_timetable_items:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisar conceptos horarios de aplicación diaria obligatoria';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(){
        $slug = 'timetables';

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
                    $calendar = WorkCalendar::select('work_calendars.id', 'work_calendars.hours', 'day_types.is_working')
                    ->leftJoin('day_types', 'work_calendars.day_type_id', '=', 'day_types.id')
                    ->where('work_calendars.workplace_id', $wp->id)
                    ->where('work_calendars.date', Carbon::today())
                    ->first();

                    //Si la jornada es laborable (que esté declarada como tal o no tenga declarado day_type_id):
                    if($calendar && ($calendar->is_working || $calendar->hours > 0)){
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
                                    ->where('date', Carbon::today())
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
                        ->get();

                        //Comprobamos que cada usuario tenga su horario laboral cerrado si no tenía festivo o jornada no presencial:
                        foreach($users as $user){
                            //Si es jornada festiva del usuario:
                            $noWork = WorkUserCalendar::noWorkDay($user->id, Carbon::today());

                            //Si no ha tomado festivo o es jornada laborable tipo 'teletrabajo':
                            if(!$noWork || ($noWork->is_working && $noWork->in_office)){
                                $items_done = [];

                                //Registros de la jornada del usuario:
                                $user_required_items = Timetable::select('timetables.id', 'timetables.date', 'timetables.hour', 'timetables.item_id', 'timetables.flag', 'timetable_items.basic_item')
                                ->join('timetable_items', 'timetables.item_id', '=', 'timetable_items.id')
                                ->where('timetables.user_id', $user->id)
                                ->where('date', Carbon::today())
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
                                            $t_out->hour = $hour_from.':00:00';
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
            }
        }
    }
}
