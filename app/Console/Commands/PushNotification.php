<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CompanyModules;
use App\Models\Contract;
use App\Models\DevicePush;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\User;
use App\Models\WorkUserCalendar;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Ladumor\OneSignal\OneSignal;

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
        // Guardamos un registro en Logs
        Log::info('Comando ejecutado: push:send - Inicio');

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
                ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
                ->where('users.status', 1)
                ->where('employees.status', 1)
                ->where('employees.time_control', 1)
                ->where('contracts.company_id', $company->id)
                ->get();


            foreach ($empleados as $empleado) {


                $noWork = WorkUserCalendar::noWorkDay($empleado->id, Carbon::today());

                //Si no ha tomado festivo o es jornada laborable tipo 'teletrabajo':
                if (!$noWork || ($noWork->is_working && $noWork->working_day)) {
                    $horario = Timetable::select('id', 'hour', 'item_id', 'flag', 'date')
                        ->where('user_id', $empleado->id)
                        ->whereDate('created_at', Carbon::now())
                        ->orderBy('date', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->first();


                    //Si el horario no está cerrado:
                    $enviado = [];
                    if ($horario && $horario->flag == 2) {
                        if (isset($horario->items->max) || isset($horario->items->min)) {
                            $hora = Carbon::parse($horario->hour)->addMinutes($horario->items->max);

                            if (Carbon::now()->format('H:i') > $hora->format('H:i')) {
                                if (!in_array($empleado->id, $enviado, true)) {
                                    $enviado[] = $empleado->id;
                                    if (User::find($empleado->id)->devicePush) {

                                        if ($horario->items->name == 'Comida') {
                                            if (Carbon::now()->dayName !== 'Friday') {
                                               // $this->message("Recuerda fichar a la vuelta de la " . $horario->items->name, User::find($empleado->id)->devicePush->token, $empleado->email);
                                                $this->messageSignal("Recuerda fichar a la vuelta de la " . $horario->items->name, User::find($empleado->id)->devicePush->token);
                                                $data_user = [
                                                    'user' => $empleado,
                                                    'fecha' => date('d-m-Y'),
                                                    'company' => $company,
                                                    'concept' => $horario->items->name,
                                                ];
                                                Mail::send('emails.send-push-mail', $data_user, function ($message) use ($horario, $empleado, $company) {
                                                    $message->from(config('constants.EMAIL_'), $company->name);
                                                    $message->to($empleado->email);
                                                    $message->subject("Recuerda fichar la {$horario->items->name}");
                                                });
                                            }
                                        } else {
                                            //$this->message("Recuerda fichar a la vuelta del " . $horario->items->name, User::find($empleado->id)->devicePush->token, $empleado->email);
                                            $this->messageSignal("Recuerda fichar a la vuelta del " . $horario->items->name, User::find($empleado->id)->devicePush->token);
                                            $data_user = [
                                                'user' => $empleado,
                                                'fecha' => date('d-m-Y'),
                                                'company' => $company,
                                                'concept' => $horario->items->name,
                                            ];
                                            Mail::send('emails.send-push-mail', $data_user, function ($message) use ($horario, $empleado, $company) {
                                                $message->from(config('constants.EMAIL_'), $company->name);
                                                $message->to($empleado->email);
                                                $message->subject("Recuerda fichar el {$horario->items->name}");
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

        // Guardamos un registro en Logs
        Log::info('Comando ejecutado: push:send - Fin');

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


        echo "send push {$tokenPush}";
    }

    /**
     * @param string $message
     * @param string $deviceId
     * @return mixed
     */
    public function messageSignal(string $message, string $deviceId)
    {
        $fields['include_player_ids'] = ["{$deviceId}"];
        $notificationMsg = $message;
        return OneSignal::sendPush($fields, $notificationMsg);
    }
}
