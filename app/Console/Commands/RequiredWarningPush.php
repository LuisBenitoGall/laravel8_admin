<?php

namespace App\Console\Commands;

use App\Models\CompanyModules;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\User;
use App\Models\WorkUserCalendar;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RequiredWarningPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warning:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica a los usuarios de que no han salido';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $slug = 'timetables';
        $companies = CompanyModules::select('companies.*')
            ->join('companies', 'company_modules.company_id', '=', 'companies.id')
            ->join('modules', 'company_modules.module_id', '=', 'modules.id')
            ->where('modules.slug', $slug)
            ->where('companies.status', 1)
            ->get();

        foreach ($companies as $company) {
            // Emails of company

            $empleados = Employee::select('users.id', 'users.name', 'users.surname', 'users.email')
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
                ->where('users.status', 1)
                ->where('employees.status', 1)
                ->where('employees.time_control', 1)
                ->where('contracts.company_id', $company->id)
                ->get();


            foreach ($empleados as $empleado) {
                $noWork = WorkUserCalendar::noWorkDay($empleado->id, Carbon::today());
                $horario = Timetable::select('id', 'hour', 'item_id', 'flag', 'date')
                    ->where('user_id', $empleado->id)
                    ->orderBy('date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();
                if ($horario && $horario->flag == 1 && $horario->items->required !== null) {
                    if(Carbon::now()->format('H:i') > $horario->items->required_warning->format('H:i')){
                        if (User::find($empleado->id)->devicePush) {
                            $this->message("Recuerda que debes fichar para " . $horario->items->name, User::find($empleado->id)->devicePush->token, $empleado->email);
                            $data_user = [
                                'user' => $empleado,
                                'fecha' => date('d-m-Y'),
                                'company' => $company,
                                'concept' => $horario->items->name,
                            ];
                            Mail::send('emails.send-push-mail', $data_user, function ($message) use ($horario, $empleado, $company) {
                                $message->from(config('constants.EMAIL_'), $company->name);
                                $message->to($empleado->email);
                                $message->subject("Recuerda que debes fichar para {$horario->items->name}");
                            });
                    }
                }
            }

        }
    }
}
