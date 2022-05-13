<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

//Models:
use App\Models\Account;
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\CompanySettings;

class CompanyAccountsController extends Controller{
    /**
     * 1. Cuentas por empresa. 
     * 1.1. Listado de cuentas por empresa para DataTable.
     * 2. Formulario renovación cuenta.
     * 3. Guardar renovación.
     * 4. Cancelar cuenta.
     */ 
    
    private $option = 'cuenta_mi';
    private $module = 'account';
    private $languages;
    
    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'account' => 'required|integer'
            ],[
                'account.required' => trans('textos.cuenta_seleccionar_aviso')
            ]);
        }
    }

    /**
     * 1. Cuentas por empresa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.empresa_cuenta', 
            'module' => $this->module,
            'slug' => 'company-accounts'
        ];

        $company = Company::find(session('currentCompany'));

        return view('admin.company_accounts.index')->with(compact('header', 'company'));
    }

    /**
     * 1.1. Listado de cuentas por empresa para DataTable.
     */
    public function getCompanyAccountsDT(){
        $accounts = CompanyAccounts::select('company_accounts.*', 'accounts.name')
        ->join('accounts', 'company_accounts.account_id', '=', 'accounts.id')
        ->where('company_accounts.company_id', session('currentCompany'))
        ->orderBy('company_accounts.start_date', 'DESC')
        ->get();

        //Ampliación de información de los objetos:
        if($accounts){
            //Permisos del usuario:
            $eliminar = Auth::user()->can('companies.destroy');

            foreach($accounts as $row){
                $row->inicio = Carbon::parse($row->start_date)->format($this->languages[session()->get('locale')][4]);
                $row->fin = $row->end_date == config('constants.UNDEFINED_DATE_')? trans('textos.indefinido'):Carbon::parse($row->end_date)->format($this->languages[session()->get('locale')][4]);

                $row->currency = $this->co_currency? $this->co_currency->symbol:config('constants.CURRENCY_');

                //Permisos para las acciones:
                $row->eliminar = $eliminar; 
            }
        }

        return datatables($accounts)->toJson();
    }

    /**
     * 2. Formulario renovación cuenta.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cuenta_renovar', 
            'module' => $this->module,
            'slug' => 'company-accounts'
        ];

        $company = Company::find(session('currentCompany'));

        $accounts = Account::where('status', 1)->get();
        $currency = $this->co_currency? $this->co_currency:false;

        return view('admin.company_accounts.create')->with(compact('header', 'company', 'accounts', 'currency'));
    }

    /**
     * 3. Guardar renovación.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/company-accounts/create')
                ->withErrors($validator)
                ->withInput();
        }  

        $account = Account::find($request->account);

        $status = 1;
        
        //Obtenemos la anterior cuenta de la empresa, de haberla:
        $last_account = CompanyAccounts::where('company_id', session('currentCompany'))
        ->orderBy('end_date', 'DESC')
        ->first();

        //La fecha de inicio debe ser posterior a la fecha de finalización de la anterior cuenta:
        if($last_account){
            if($last_account->end_date >= Carbon::today()){
                $start_date = Carbon::parse($last_account->end_date)->addDay()->format('Y-m-d');
                $status = '0';
            }else{
                $start_date = Carbon::today();    
            }
            
        }else{
            $start_date = Carbon::today();
        }

        //Fecha fin:
        $end_date = NULL;
        if($account->duration){
            $end_date = Carbon::parse($start_date)->addDays($account->duration)->format('Y-m-d');
        }

        $cc = new CompanyAccounts();
        $cc->company_id = session('currentCompany');
        $cc->account_id = $request->account;
        $cc->start_date = $start_date;
        $cc->end_date = $end_date;
        $cc->price = $account->rate;
        $cc->status = $status;
        $cc->save();

        $msg = trans('textos.cuenta_creado_ok');
        return redirect()->route('company-accounts.index')->with(compact('msg'));
    }

    /**
     * 4. Cancelar cuenta.
     */
    public function destroy(CompanyAccounts $account){
        //La cuenta debe pertenecer a la empresa en sesión:
        if($account->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('company-accounts.index')->with(compact('alert'));  
            exit;      
        }

        $account->end_date = date('Y-m-d');
        $account->status = 2;
        $account->save();

        $msg = trans('textos.cuenta_cancelada_ok');
        return redirect()->route('company-accounts.index')->with(compact('msg'));
    }
}
