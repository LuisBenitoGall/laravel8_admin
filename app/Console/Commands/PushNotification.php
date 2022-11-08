<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CompanyModules;
use App\Models\Contract;
use App\Models\DevicePush;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\WorkUserCalendar;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviamos notificaciones push a usuarios por compañias';

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
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
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
            // $emailsTo = unserialize($company->settings->emails);

            $empleados = Employee::select('users.id', 'users.name', 'users.surname', 'users.email')
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
                ->where('users.status', 1)
                ->where('employees.status', 1)
                ->where('employees.time_control', 1)
                ->where('user_companies.company_id', $company->id)
                ->get();

            foreach ($empleados as $empleado) {

                $noWork = WorkUserCalendar::noWorkDay($empleado->id, Carbon::today());

                //Si no ha tomado festivo o es jornada laborable tipo 'teletrabajo':
                if (!$noWork || ($noWork->is_working && $noWork->working_day)) {
                    $horario = Timetable::select('id', 'hour', 'item_id', 'flag', 'date')
                        ->where('user_id', $empleado->id)
                        ->orderBy('date', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->first();

                    //Si el horario no está cerrado:
                    $enviado = [];
                    if ($horario && $horario->flag == 2) {
                        if (isset($horario->items->max) || isset($horario->items->min)) {
                            $hora = Carbon::parse($horario->hour)->addMinutes($horario->items->max);

                            if (Carbon::parse('14:24')->format('H:i') > $hora->format('H:i')) {
                                $enviado[] = $empleado->id;
                                echo $enviado;
                                if (!in_array($empleado->id, $enviado)) {
                                    $this->message('Has Olvidado Fichar!!!', DevicePush::find($empleado->id)->token, $empleado->email);
                                }
                            }
                        }

                    }
                }
            }
        }


    }


    /**
     * @param string $msg
     * @param $tokenPush
     * @param $user
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function message(string $msg, $tokenPush, $user)
    {

        $client = new Client();
        $response = $client->request('POST', 'https://api.electrolytic.app/push/send', ['json' => [
            'appKey' => 'XxSDtdKWsvc922mnKYmF',
            'appSecret' => 'c788a2f4b5ef79944ecfe103',
            'target' => ["$tokenPush"],
            'payload' => $msg
        ]]);


        echo $user;
    }
}
