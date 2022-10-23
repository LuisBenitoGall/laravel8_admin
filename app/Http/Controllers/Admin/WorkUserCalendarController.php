<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\DayTypes;
use App\Models\DayTypesConfig;
use App\Models\Employee;
use App\Models\GroupMembers;
use App\Models\ResiduaryDays;
use App\Models\Timetable;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\UserTimetables;
use App\Models\UserWorkplaces;
use App\Models\WorkCalendar;
use App\Models\Workplace;
use App\Models\WorkUserCalendar;

class WorkUserCalendarController extends Controller{
    /**
     * 1. Calendario laboral de usuario.
     * 2. Notificar jornadas solicitadas.
     * 3. Validar todas las jornadas de usuario.
     * 4. Adjuntar documento.
     * 5. Eliminar documento.
     */
    
    private $option = 'calendario';
    private $module = 'calendar';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:calendar');

        $this->middleware('user_companies_get', ['only' => ['index', 'notify']]);
        $this->middleware('user_companies_post2', ['only' => ['validateAll']]);
    }

     //0. Validator:
    protected function validator(array $data){
        //Validación validate:
        if(isset($data['submit_validate'])){
            return Validator::make($data, [
                'year' => 'required|integer',
                'user_id' => 'required|integer'
            ]); 

        //Validación add doc:
        }elseif(isset($data['submit_add_doc'])){
            return Validator::make($data, [
                'user_id' => 'required|integer',
                'year' => 'required|integer',
                'file' => 'nullable|mimes:jpeg,jpg,png,gif,pdf|max:1000'
            ],[
                'file.max' => trans('textos.archivo_grande_aviso')
            ]);     
        }
    }

    /**
     * 1. Calendario laboral de usuario.
     */
    public function index(User $user, $year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.calendario_laboral_empleado'), 
            'module' => $this->module,
            'slug' => 'work-calendar'
        ];

        $today = date('Y-m-d');
        $year = $year? $year:Date('Y');

        $workplace = Workplace::select('workplaces.*')
        ->join('user_workplaces', 'workplaces.id', '=', 'user_workplaces.workplace_id')
        ->where('user_workplaces.user_id', $user->id)
        ->first();

        //Tipos de jornada para usuario:
        $day_types = DayTypes::where('company_id', session('currentCompany'))
        ->where('user_enabled', 1)
        ->orderBy('name', 'ASC')
        ->get();

        return view('admin.work_user_calendar.index')->with(compact('header', 'user', 'today', 'year', 'workplace', 'day_types'));
    }

    /**
     * 2. Notificar jornadas solicitadas.
     */
    public function notify(User $user){
        $fechas = WorkUserCalendar::select('work_user_calendars.id', 'work_user_calendars.date AS fecha', 'work_user_calendars.notified', 'day_types.name AS dayType')
        ->join('day_types', 'work_user_calendars.day_type_id', '=', 'day_types.id')
        ->where('work_user_calendars.user_id', $user->id)
        ->whereNull('work_user_calendars.self_hours')
        ->whereNull('work_user_calendars.notified')
        ->whereDate('work_user_calendars.date', '>=', Carbon::today())
        ->get();

        $arr = [];
        foreach($fechas as $row){
            if(!isset($arr[$row->dayType])){
                $arr[$row->dayType] = [];
            }

            $arr[$row->dayType][] = $row->fecha;

            $row->notified = Carbon::today();
            $row->save();
        }

        //Destinatarios del email:
        //1. Emails corporativos:
        $receivers = session('companySettings')->emails? unserialize(session('companySettings')->emails):[];

        //2. Emails responsables de grupo:
        $groups = GroupMembers::where('user_id', $user->id)
        ->pluck('group_id')
        ->toArray();

        $from_groups = GroupMembers::join('users', 'group_members.user_id', '=', 'users.id')
        ->where('group_members.responsible', 1)
        ->whereIn('group_id', $groups)
        ->whereNull('group_members.deleted_at')
        ->groupBy('users.email')
        ->pluck('users.email')
        ->toArray();

        $receivers = array_merge($receivers, $from_groups);
        $receivers = array_unique($receivers);

        $emailFrom = $user->email;
        $data['usuario'] = $user->name.' '.$user->surname;
        $company = Company::find(session('currentCompany'));
        $data['company'] = $company;
        $data['days'] = $arr; 
        $data['languages'] = $this->languages;

        foreach($receivers as $rcv){
            $emailTo = $rcv;

            Mail::send('emails.send-request-days-notification', $data, function($message) use($emailFrom, $company, $emailTo, $data){
                $message->from($emailFrom, $company->name);
                $message->to($emailTo);
                $message->subject(trans('textos.solicitud_jornadas').' '.$data['usuario']); 
            }); 
        }

        $msg = trans('textos.notificacion_enviada');
        return redirect()->route('work-user-calendar.index', $user->id)->with(compact('msg'));
    }

    /**
     * 3. Validar todas las jornadas de usuario.
     */
    public function validateAll(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/work-user-calendar/'.$request->user_id.'/'.$request->year)
            ->withErrors($validator)
            ->withInput();
        }

        //Validamos los tipos de jornada recibidos:
        if(isset($request->day_type) && is_array($request->day_type)){
            $arr = [];
            $now = Carbon::now();

            foreach($request->day_type as $dt){
                //Seleccionamos las fechas relativas:
                $fechas = WorkUserCalendar::select('work_user_calendars.*', 'day_types.name AS dayType', 'day_types.is_working')
                ->leftJoin('day_types', 'work_user_calendars.day_type_id', '=', 'day_types.id')
                ->where('work_user_calendars.user_id', $request->user_id) 
                ->where('work_user_calendars.day_type_id', $dt)
                ->whereYear('work_user_calendars.date', $request->year)
                ->get();

                foreach($fechas as $fecha){
                    //Si el tipo de día es compatible laborable o no:   
                    if(!$fecha->is_working){
                        //Eliminamos los registros de esa fecha para las jornada no laborables:
                        Timetable::deleteRowsByUserDate($request->user_id, $fecha->date);

                        $fecha->self_hours = NULL;
                    }

                    $fecha->status = 1;
                    $fecha->save();

                    //Las fechas de confirmación reciente (menos de 30" respecto su fecha de updated_at) se guardan para enviar en la confirmación:
                    $seconds_diff = $now->diffInSeconds($fecha->updated_at);

                    if($seconds_diff < 30){
                        if(!isset($arr[$fecha->dayType])){
                            $arr[$fecha->dayType] = [];
                        }

                        $arr[$fecha->dayType][] = $fecha->date;    
                    }
                }
            }

            $user = User::find($request->user_id);

            $emailFrom = config('constants.EMAIL_');
            $emailTo = $user->email;
            $data['usuario'] = $user->name.' '.$user->surname;
            $company = Company::find(session('currentCompany'));
            $data['company'] = $company;
            $data['days'] = $arr; 
            $data['languages'] = $this->languages;

            Mail::send('emails.send-days-confirmation', $data, function($message) use($emailFrom, $company, $emailTo){
                $message->from($emailFrom, $company->name);
                $message->to($emailTo);
                $message->subject(trans('textos.jornadas_solicitadas_confirmacion')); 
            }); 

            $msg = trans('textos.validadas_jornadas_ok');
            return redirect()->route('work-user-calendar.index', [$request->user_id, $request->year])->with(compact('msg')); 
        }else{
            $alert = trans('textos.validadas_jornadas_vacio');
            return redirect()->route('work-user-calendar.index', [$request->user_id, $request->year])->with(compact('alert')); 
        }
    }

    /**
     * 4. Adjuntar documento.
     */
    public function addDoc(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-docs');
            
            return redirect('admin/work-user-calendar/'.$request->user_id.'/'.$request->year)
                ->withErrors($validator)
                ->withInput();
        }


        if($request->hasFile('file')){
            $file = $request->file('file');
            $path = public_path().'/img/calendar';
            $fileName = uniqid().$file->getClientOriginalName();
            $moved = $file->move($path, $fileName);

            //si el archivo ha sido guardado correctamente:
            if($moved){
                $wuc = WorkUserCalendar::find($request->wuc_id);
                $wuc->doc = $fileName;
                $wuc->save();
            }else{
                return false;
            }

            $msg = trans('textos.archivo_uploaded');
            return redirect()->route('work-user-calendar.index', [$request->user_id, $request->year])->with(compact('msg'));   

        }else{
            $alert = trans('textos.archivo_uploaded_error');
            return redirect()->route('work-user-calendar.index', [$request->user_id, $request->year])->with(compact('alert'));     
        }  
    }

    /**
     * 5. Eliminar documento.
     */
    public function deleteDoc(WorkUserCalendar $doc){
        //Recuperamos año de la fecha:
        $p_year = $doc->date? explode('-', $doc->date):false;
        $year = count($p_year)? $p_year[0]:Date('Y');

        //Comprobamos que usuario pertenezca a empresa en session:
        $exist = UserCompanies::userInCompany($doc->user_id, session('currentCompany'));
        if(!$exist){
            $alert = trans('textos.accion_no_permitida');
            return redirect()->route('work-user-calendar.index', [$doc->user_id, $year])->with(compact('alert'));     
        }

        //Eliminamos documento:
        File::delete(public_path().'/img/calendar/'.$doc->doc);

        $doc->doc = NULL;
        $doc->save();

        $msg = trans('textos.documento_deleted');
        return redirect()->route('work-user-calendar.index', [$doc->user_id, $year])->with(compact('msg'));  
    }
}
