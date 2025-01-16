<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\EmployeesExpiration;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

//Models:
use App\Models\CompanyNotificationsManagement;
use App\Models\Employee;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmployeesExpirationTask extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees_expiration:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Control del vencimiento de contratos';

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

        // Guardamos un registro en Logs
        Log::info('Comando ejecutado: employees_expiration:task - Inicio');

        $today = Carbon::today()->toDateString();
        $expiration = Carbon::today()->addDays(config('constants.NOTIFICATION_PERIOD_'))->toDateString();
        $module = Module::moduleBySlug('rrhh');
        //Idiomas:
        $languages = Helpers::languages();

        $employees = Employee::select('employees.*', 'users.name', 'users.surname', 'contracts.company_id')
        ->join('users', 'employees.user_id', '=', 'users.id')
        ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        ->whereBetween('employees.end_date', [$today, $expiration])
        ->get();

        //Envío de notificaciones:
        foreach($employees as $employee){
            //Obtenemos los usuarios habilitados para recibir las notificaciones de cada empresa.
            $users = User::select('users.id')
            ->join('company_notifications_management', 'users.id', '=', 'company_notifications_management.user_id')
            ->where('company_notifications_management.company_id', $employee->company_id)
            ->where('company_notifications_management.module_id', $module->id)
            ->get();    

            foreach($users as $user){
                //Ampliamos info del objeto:
                $user->title = trans('textos.contrato_vencimiento_aviso').' '.$employee->name.' '.$employee->surname;
                $user->sender = trans('textos.automatico');
                $user->employee_id = $employee->id;
                $user->employee = $employee->name.' '.$employee->surname;
                $user->end_date = $employee->end_date;
                $user->end_date_trans = Carbon::parse($employee->end_date)->format('d/m/Y');


                //Carbon::parse($employee->end_date)->format('d/m/Y');

                //$this->languages[session()->get('locale')][4]

                $user->notify(new EmployeesExpiration($user));
            }
        }

        Log::info('Comando ejecutado: employees_expiration:task - Fin');
    }
}
