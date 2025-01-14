<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CompanyModules;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableItems;
use App\Models\User;
use App\Models\WorkUserCalendar;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Ladumor\OneSignal\OneSignal;

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
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        // Guardamos un registro en Logs
        Log::info('Comando ejecutado: warning:push - Inicio');

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
                            if ($item->basic_item) {
                                $verify = Timetable::where('item_id', $item->id)
                                    ->where('user_id', $empleado->id)
                                    ->whereDate('created_at', Carbon::now())->exists();

                                if (!$verify) {
                                    if (Carbon::now()->format('H:i:s') > Carbon::parse($item->required_warning)->format('H:i')) {
                                        if (User::find($empleado->id)->devicePush) {
                                            if ($item->name == 'Comida') {
                                                if (Carbon::now()->dayName !== 'Friday') {
                                                   // $this->message("Recuerda fichar la " . $item->name, User::find($empleado->id)->devicePush->token, $empleado->email);
                                                    $this->messageSignal("Recuerda fichar la " . $item->name, User::find($empleado->id)->devicePush->token);
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
                                            } else {

                                                //$this->message("Recuerda fichar el " . $item->name, User::find($empleado->id)->devicePush->token, $empleado->email);
                                                $this->messageSignal("Recuerda fichar el " . $item->name, User::find($empleado->id)->devicePush->token);
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

        Log::info('Comando ejecutado: warning:push - Fin');
    }


    /**
     * @param string $msg
     * @param $tokenPush
     * @param $user
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public
    function message(string $msg, $tokenPush, $user)
    {

        $client = new Client();
        $response = $client->request('POST', 'https://api.electrolytic.app/push/send', ['json' => [
            'appKey' => 'XxSDtdKWsvc922mnKYmF',
            'appSecret' => 'c788a2f4b5ef79944ecfe103',
            'target' => ["$tokenPush"],
            'payload' => $msg
        ]]);


        echo "send push {$tokenPush}\n";
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
