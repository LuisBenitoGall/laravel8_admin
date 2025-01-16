<?php

namespace App\Console\Commands;

use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\WorkUserCalendar;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestingEmailsFromConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weareadn:testing-emails-from-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testing emails from config';

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
        $this->CommandCheckTimetableTask();
        return 0;
    }

    private function CommandCheckTimetableTask()
    {
        // Guardamos un registro en Logs
        Log::info('Comando ejecutado: test: check_timetable:task - Inicio');

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

                //Recuperamos todos los cierres automáticos del día y se envían por email a los responsables:
                $cierres = Timetable::select('timetables.hour', 'users.name', 'users.surname')
                ->join('users', 'timetables.user_id', '=', 'users.id')
                ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
                ->where('timetables.comments', $msgCierre)
                ->where('timetables.flag', 2)
                ->where('user_companies.company_id', $co->id)
                ->whereDate('timetables.date', Carbon::yesterday())
                ->get();

                $this->info('Cierres: '.$cierres->count());

                if($cierres->count()){
                    $data = [
                        'cierres' => $cierres,
                        'fecha' => date('d-m-Y'),
                        'company' => $co
                    ];

                    Mail::send('emails.send-timetable-unclosed-list', $data, function($message) use($emailsTo, $co){
                        $message->from(config('mail.from.address'), $co->name);
                        $emailsToList = array_merge($emailsTo, ["ario@weareadn.com"]);
                        $message->to($emailsToList);
                        $message->subject(trans('textos.horarios_cerrados_automaticamente_lista'));
                    });
                }
            }
        }

        Log::info('Comando ejecutado: test: check_timetable:task - Fin');
    }
}
