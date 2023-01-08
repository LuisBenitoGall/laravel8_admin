<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

//Models:
use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\User;
use App\Models\WorkUserCalendar;

class CheckTimetableTask extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check_timetable:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chequear horarios de trabajo registrados';

    /**
     * Create a new command instance. La función cierra la jornada del empleado que haya olvidado indicar hora de salida. Se marca como hora de salida las 17h y se envía un email a las personas responsables para que revisen posteriormente esta incidencia. Se ejecuta todos los días laborables a las 23:50h.
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
}
