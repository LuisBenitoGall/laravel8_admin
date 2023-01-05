<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\helpers;
use Carbon\Carbon;

//Models:
use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableItems;
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
