<?php

namespace App\Console\Commands;

use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\User;
use App\Models\WorkUserCalendar;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ClosedTimeSigned extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'closed:signed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cerramos comida ó Desayuno';

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
                    ->whereDate('created_at', Carbon::now())
                    ->orderBy('date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();

                if (!$noWork || ($noWork->is_working && $noWork->working_day)) {
                    if (isset($horario)) {

                        $timeTableItems = TimetableItems::where('company_id', $company->id)
                            ->whereNotNull('required')
                            ->get();


                        foreach ($timeTableItems as $item) {

                            $verify = Timetable::where('item_id', $item->id)
                                ->where('user_id', $empleado->id)
                                ->whereDate('created_at', Carbon::now())->exists();

                            if (!$verify) {
                                if (Carbon::now()->format('H:i:s') > $item->required_warning) {
                                    if (User::find($empleado->id)->devicePush) {
                                        if ($item->name == 'Comida') {
                                            if (Carbon::now()->dayName !== 'Friday') {
                                                $data_user = [
                                                    'user' => $empleado,
                                                    'fecha' => date('d-m-Y'),
                                                    'company' => $company,
                                                    'concept' => $item->name,
                                                ];
                                                Mail::send('emails.send-push-mail-warning', $data_user, function ($message) use ($item, $horario, $empleado, $company) {
                                                    $message->from(config('constants.EMAIL_'), $company->name);
                                                    $message->to($empleado->email);
                                                    $message->subject("Recuerda fichar la {$item->name}");
                                                });
                                            }
                                        }

                                        $this->message("Recuerda fichar el " . $item->name, User::find($empleado->id)->devicePush->token, $empleado->email);
                                        $data_user = [
                                            'user' => $empleado,
                                            'fecha' => date('d-m-Y'),
                                            'company' => $company,
                                            'concept' => $item->name,
                                        ];
                                        Mail::send('emails.send-push-mail-warning', $data_user, function ($message) use ($item, $horario, $empleado, $company) {
                                            $message->from(config('constants.EMAIL_'), $company->name);
                                            $message->to($empleado->email);
                                            $message->subject("Recuerda fichar el {$item->name}");
                                        });
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
