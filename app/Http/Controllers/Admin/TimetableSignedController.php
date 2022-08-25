<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

//Models:
use App\Models\Company;
use App\Models\Employee;
use App\Models\Timetable;
use App\Models\TimetableSigned;
use App\Models\User;
use App\Models\Workplace;

class TimetableSignedController extends Controller{
    /**
     * 1. Relación de horarios firmados por empresa.
     * 1.1. Filtro relación de horarios.
     * 2. Relación de horarios firmados por usuario.
     * 3. Validar firma de horarios.
     * 4. Firmar horario.
     * 5. Descargar horario en .pdf.
     */
    
    private $option = 'horarios'; 
    private $module = 'timetables';
    private $languages;
    
    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:timetables');
    }

    /**
     * 1. Relación de horarios firmados por empresa.
     */
    public function index($year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.firma_horarios',
            'module' => $this->module,
            'slug' => 'timetable-signed'
        ]; 

        if(!$year) $year = date('Y');

        $timetables = [];
        $data = TimetableSigned::select('month')
        ->where('company_id', session('currentCompany'))
        ->where('year', $year)
        ->groupBy('month')
        ->orderBy('month', 'ASC')
        ->get();
        if($data->count() > 0){
            foreach($data as $row){
                array_push($timetables, $row->month);
            }
        }

        //Meses del año:
        $months = helpers::yearMonths();    

        return view('admin.timetable_signed.index')->with(compact('header', 'year', 'timetables', 'months'));
    }

    /**
     * 1.1. Filtro relación de horarios.
     */
    public function getTimetablesBy(Request $request){
        return redirect()->route('timetable-signed.index', [$request->year]);
    }
    
    /**
     * 2. Relación de horarios firmados por usuario.
     */
    public function show(User $user){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.firma_horarios',
            'module' => $this->module,
            'slug' => 'timetable-signed'
        ]; 

        $timetables = TimetableSigned::where('user_id', $user->id)
        ->orderBy('year', 'DESC')
        ->orderBy('month', 'DESC')
        ->get();

        return view('admin.timetable_signed.show')->with(compact('header', 'timetables', 'user'));
    }

    /**
     * 3. Validar firma de horarios.
     */
    public function validateTimetable($year, $month){
        if($year && $month){
            //Comprobamos que no existan registros anteriores:
            $data = TimetableSigned::select('id')
            ->where('company_id', session('currentCompany'))
            ->where('year', $year)
            ->where('month', $month)
            ->get();

            if($data->count() == 0){
                //Obtenemos empleados de la empresa:
                $employees = Employee::select('employees.user_id')
                ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
                ->where('contracts.company_id', session('currentCompany'))
                ->where('employees.time_control', 1)
                ->get();  

                if($employees->count() > 0){
                    foreach($employees as $row){
                        //Comprobamos que no se duplique el registro:
                        $exist = TimetableSigned::select('id')
                        ->where('user_id', $row->user_id)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->first();

                        if(!$exist){
                            $ts = new TimetableSigned();
                            $ts->user_id = $row->user_id;
                            $ts->company_id = session('currentCompany');
                            $ts->month = $month;
                            $ts->year = $year;
                            $ts->save();
                        }
                    }
                }
            }

            $msg = trans('textos.validadas_jornadas_ok');
            return redirect()->route('timetable-signed.index', [$year])->with(compact('msg'));
        }
    }

    /**
     * 4. Firmar horario.
     */
    public function sign(TimetableSigned $ts){
        if($ts->user_id == Auth::user()->id){
            //Datos del empleado:
            $user = User::select('id', 'signature')->find($ts->user_id);

            if(!$user->signature){
                $alert = trans('textos.no_permisos_edicion');
                return redirect()->route('timetable-signed.show', Auth::user()->id)->with(compact('alert'));   
                exit; 
            
            }else{
                //Confirmamos su firma:
                $ts->signed = 1;
                $ts->save();   

                return redirect()->route('timetable-signed.download', $ts->id);
            }
        
        }else{
            $alert = trans('textos.no_permisos_edicion');
            return redirect()->route('timetable-signed.show', Auth::user()->id)->with(compact('alert'));
        }
    }

    /**
     * 5. Descargar horario en .pdf.
     */
    public function download(TimetableSigned $ts){
        if($ts->user_id == Auth::user()->id){ 
            $user = User::find(Auth::user()->id);

            $from = $ts->year.'-'.$ts->month.'-01';
            $to = Carbon::parse($from)->endOfMonth()->toDateString();   //Obtenemos último día del mes.

            $desde = (config('app.locale') != 'en')? Carbon::parse($from)->format(config('locale.languages.'.config('app.locale'))[4]):$from;
            $hasta = (config('app.locale') != 'en')? Carbon::parse($to)->format(config('locale.languages.'.config('app.locale'))[4]):$to;

            $fromH = $from.' 00:00:00';
            $toH = $to.' 23:59:59';

            $horarios = Timetable::getUserTimetable($user->id, $fromH, $toH);

            $company_settings = session('companySettings');

            //Empresa:
            $company = Company::find($company_settings->company_id);

            //Centro de trabajo principal de la empresa:
            $workplace = Workplace::select('workplaces.address', 'workplaces.cp', 'towns.name AS town')
            ->where('workplaces.company_id', $company->id)
            ->join('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.featured', 1)
            ->first();

            //Color corporativo:
            $bg_color = $company_settings->primary_color? $company_settings->primary_color:'#10172C';

            $pdf = PDF::loadView('pdf/user-timetable', compact('ts', 'user', 'horarios', 'desde', 'hasta', 'company_settings', 'company', 'workplace', 'bg_color'));
            $pdf->setPaper('L', 'portrait');

            //Guardar o imprimir .pdf:
            if($ts->signed){
                $p = explode('-', $from);
                $pdfName = Str::slug($user->name).'-'.Str::slug($user->surname).'-'.$p[0].'-'.$p[1].'.pdf';

                if(!file_exists('docs/timetables_signed/'.$pdfName)){
                    $pdf->save('docs/timetables_signed/'.$pdfName);

                    $ts->doc = $pdfName;
                    $ts->save();
                }

                $msg = trans('textos.firma_horarios_guardada');
                return redirect()->route('timetable-signed.show', Auth::user()->id)->with(compact('msg'));

            }else{
                return $pdf->download(trans('textos.horarios').'-'.$user->name.'-'.$user->surname.'.pdf'); 
            }

        }else{
            $alert = trans('textos.no_permisos_edicion');
            return redirect()->route('timetable-signed.show', Auth::user()->id)->with(compact('alert'));
        }   
    }

}
