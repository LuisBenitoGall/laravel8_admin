<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use App\Notifications\EmployeesExpiration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use File;

//Models:
use App\Models\CompanyNotificationsManagement;
use App\Models\CompanySettings;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Module;
use App\Models\User;
use App\Models\UserWorkplaces;
use App\Models\Workplace;

class EmployeeController extends Controller{
    /**
     * 1. Empleados por empresa.
     * 1.1. Listado de empleados para DataTable.
     * 1.2. Empleados por empresa para Ajax.
     * 2. Empleos por usuario.
     * 3. Formulario para nuevo empleo.
     * 4. Guardar nuevo empleo.
     * 5. Mostrar info del empleo.
     * 6. Editar empleo.
     * 7. Eliminar empleo.
     * 7.1. Actualizar status del empleo.
     * 8. Cancelar contrato de empleado.
     */
    
    private $option = 'empleados';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:rrhh');
        $this->middleware('user_companies_get', ['only' => ['userEmployees', 'create', 'store']]);
        $this->middleware('employee_get', ['only' => ['destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'workplace_id' => 'required|integer',
                'contract_id' => 'required|integer',
                'job_title' => 'nullable|string|max:255',
                'start_date' => 'required',
                'weekly_hours' => 'required|numeric|max:99'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'job_title' => 'nullable|string|max:255',
                'start_date' => 'required',
                'weekly_hours' => 'required|numeric|max:99'
            ]);
        }
    }
    
    /**
     * 1. Empleados por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.empleados_mis',
            'module' => $this->module,
            'slug' => 'employees'
        ]; 












        // $today = Carbon::today()->toDateString();
        // $expiration = Carbon::today()->addDays(config('constants.NOTIFICATION_PERIOD_'))->toDateString();
        // $module = Module::moduleBySlug('rrhh');

        // $employees = Employee::select('employees.*', 'users.name', 'users.surname', 'contracts.company_id')
        // ->join('users', 'employees.user_id', '=', 'users.id')
        // ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        // ->whereBetween('employees.end_date', [$today, $expiration])
        // ->get();

        // //Envío de notificaciones:
        // foreach($employees as $employee){
        //     //Obtenemos los usuarios habilitados para recibir las notificaciones de cada empresa.
        //     $users = User::select('users.*')
        //     ->join('company_notifications_management', 'users.id', '=', 'company_notifications_management.user_id')
        //     ->where('company_notifications_management.company_id', $employee->company_id)
        //     ->where('company_notifications_management.module_id', $module->id)
        //     ->get();    

        //     foreach($users as $user){
        //         // $notification['title'] = trans('textos.contrato_vencimiento_aviso');
        //         // $notification['employee_id'] = $employee->id;
        //         // $notification['employee'] = $employee->name.' '.$employee->surname;
        //         // $notification['end_date'] = $employee->end_date;

        //         //dd($user, $notification);
                
        //         $user->title = trans('textos.contrato_vencimiento_aviso');
        //         $user->employee_id = $employee->id;
        //         $user->employee = $employee->name.' '.$employee->surname;
        //         $user->end_date = $employee->end_date;

        //         $user->notify(new EmployeesExpiration($user));
        //     }
        // }










        return view('admin.employees.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de empleados para DataTable.
     */
    public function getEmployeesDT(){
        //¡Atención! a la query para que devuelva el contrato más reciente:
        $employees = Employee::select('employees.*', 'users.name', 'users.surname')
        ->whereRaw('employees.id IN (select MAX(id) FROM employees WHERE deleted_at IS NULL GROUP BY user_id)')
        ->join('users', 'employees.user_id', '=', 'users.id')
        ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        ->where('contracts.company_id', session('currentCompany'))
        ->get();
        
        //Permisos del usuario:
        $editar = Auth::user()->can('employees.edit');
        $eliminar = Auth::user()->can('employees.destroy');

        //Ampliación de información de los objetos:
        if($employees){
            foreach($employees as $row){
                //Comprobamos que los contratos no hayan vencido:
                $row = $this->status($row);

                $row->empleado = $row->name.' '.$row->surname;
                //Enviamos las fechas ya formateadas:
                $row->inicio = Carbon::parse($row->start_date)->format($this->languages[session()->get('locale')][4]);
                $row->fin = Carbon::parse($row->end_date)->format($this->languages[session()->get('locale')][4]);

                if($row->end_date == config('constants.UNDEFINED_DATE_')){
                    $row->fin = ucfirst(trans('textos.indefinido'));
                }

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar;
            }
        }

        return datatables($employees)->toJson();     
    }

    /**
     * 1.2. Empleados por empresa para Ajax.
     */
    public function getCompanyEmployeesAjax($company_id, $status = false){
        $data = Employee::companyEmployees($company_id, 1);
        return response()->json($data);
    }

    /**
     * 2. Empleos por usuario.
     */
    public function userEmployees(User $user){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.empleado_contratos',
            'module' => $this->module,
            'slug' => 'employees'
        ]; 

        $employees = Employee::select('employees.*', 'contracts.name AS Contract', 'companies.id AS companyId')
        ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        ->join('companies', 'contracts.company_id', '=', 'companies.id')
        ->where('employees.user_id', $user->id)
        ->where('companies.id', session('currentCompany'))
        ->orderBy('employees.start_date', 'DESC')
        ->get();

        //Modificando datos:
        if($employees->count() > 0){
            foreach($employees as $emp){
                //Comprobamos que los contratos no hayan vencido:
                $emp = $this->status($emp);
            }
        }
        
        $languages = $this->languages;

        return view('admin.employees.user')->with(compact('header', 'user', 'employees', 'languages'));
    }

    /**
     * 3. Formulario para nuevo empleo.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.contrato_nuevo',
            'module' => $this->module,
            'slug' => 'employees'
        ]; 

        $previous_url = url()->previous();

        $contracts = Contract::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Centros de trabajo:
        $workplaces = Workplace::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        $languages = $this->languages;

        return view('admin.employees.create')->with(compact('header', 'user', 'previous_url', 'contracts', 'currency', 'workplaces', 'languages'));
    }

    /**
     * 4. Guardar nuevo empleo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/employees/'.$user->id.'/create')
                ->withErrors($validator)
                ->withInput();
        }  

        //Formateando fechas de inicio y fin:
        $start_date = helpers::convertFecha($request->input('start_date'));

        if($request->input('end_date')){
            $end_date = helpers::convertFecha($request->input('end_date'));
        }else{
            $end_date = config('constants.UNDEFINED_DATE_');
        }

        $time_control = $request->input('time_control')? 1:0;
        $status = ($start_date > date('Y-m-d'))? 0:1;

        //Empleo:
        $employee = new Employee();
        $employee->user_id = $user->id;
        $employee->contract_id = $request->input('contract_id');
        $employee->workplace_id = $request->input('workplace_id');
        $employee->job_title = $request->input('job_title');
        $employee->start_date = $start_date;
        $employee->end_date = $end_date;
        $employee->weekly_hours = $request->input('weekly_hours');
        $employee->time_control = $time_control;
        $employee->year_salary = $request->input('year_salary');
        $employee->status = $status;
        $employee->observations = $request->input('observations');
        $employee->save();

        //Centro de trabajo:
        $workplace = UserWorkplaces::firstOrCreate(['user_id' => $user->id], ['workplace_id' => $request->workplace_id]);

        $msg = trans('textos.contrato_creado');
        return redirect()->route('employees.user', $user)->with(compact('msg'));
    }

    /**
     * 5. Mostrar info del empleo.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){

    }

    /**
     * 6. Editar empleo.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = false){
        if($id){
            $header = [
                'option' => $this->option, 
                'suboption' => 'textos.contrato_editar',
                'module' => $this->module,
                'slug' => 'employees'
            ]; 

            $employee = Employee::select('employees.*', 'users.name', 'users.surname', 'contracts.name AS Contract', 'contracts.duration')
            ->join('users', 'employees.user_id', '=', 'users.id') 
            ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
            ->find($id);

            $end_date = $employee->end_date != ''? Carbon::parse($employee->end_date)->format($this->languages[session()->get('locale')][4]):'';

            //Actualizamos fecha de finalización de contrato:
            $this->status($employee);

            //Moneda:
            $currency = CompanySettings::co_currency(0);

            $languages = $this->languages;

            return view('admin.employees.edit')->with(compact('header', 'employee', 'currency', 'languages'));

        }else{
            return redirect('/');
            exit;
        }
    }

    /**
     * 7. Eliminar empleo.
     */
    public function destroy(Employee $employee){
        //Actualizamos la fecha de finalización de los horarios personalizados:
        //27/04/2022 pendiente de revisar esta funcionalidad:
        //UserTimetables::updateToTimetables($employee->id, date('Y-m-d'));

        $employee->delete();

        $msg = trans('textos.contrato_deleted');
        return redirect()->route('employees.user', $employee->user_id)->with(compact('msg'));         
    }

    /**
     * 7.1. Actualizar status del empleo.
     */
    public function status($employee){
        if($employee->end_date && $employee->end_date < date('Y-m-d')){
            $employee->status = config('constants.EMPLOYEE_EXPIRED_');
            $employee->save();
        }

        return $employee;
    }

    /**
     * 8. Cancelar contrato de empleado.
     */
    public function cancel(Request $request){
        $end = $request->input('end')? helpers::convertFecha($request->input('end')):date('Y-m-d');

        $status = $end < date('Y-m-d')? 0:1;

        $employee = Employee::find($request->input('id'));
        $employee->end_date = $end;
        $employee->status = $status;
        $employee->observations = $request->input('observations');
        $employee->save();

        //Actualizamos la fecha de finalización de los horarios personalizados:
        UserTimetables::updateToTimetables($employee->id, $end);

        $msg = trans('textos.contrato_cancelled');
        return redirect()->route('employees.user', $request->input('user_id'))->with(compact('msg'));  
    }    
}
