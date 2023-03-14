<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Account;
use App\Models\AccountingConcept;
use App\Models\BankAccounts;
use App\Models\Bank;
use App\Models\BusinessAreas;
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\CompanySettings;
use App\Models\CostCenters;
use App\Models\CustomerProviders;
use App\Models\Employee;
use App\Models\PaymentMethod;
use App\Models\TempImportCompanies;
use App\Models\Town;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\Workplace;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerProvidersController extends Controller{
    /**
     * 1. Listado de clientes.
     * 1.1. Listado de clientes para Datatable.
     * 2. Listado de proveedores.
     * 2.1. Listado de proveedores para Datatable.
     * 3. Formulario nuevo cliente.
     * 3.1. Editar cliente.
     * 3.2. Mostrar ficha cliente o proveedor.
     * 4. Formulario nuevo proveedor.
     * 4.1. Editar proveedor.
     * 5. Vincular / desvincular cliente y proveedor.
     * 6. Eliminar cliente o proveedor.
     * 7. Obtener empresas distintas a la de sesión.
     * 8. Obtener info de empresas por criterio.
     * 9. Formulario importación .xls.
     * 10. Procesando registro de importación .xls.
     * 11. Exportar clientes a otras empresas vinculadas.
     * 12. Exportar proveedores a otras empresas vinculadas.
     * 13. Añadir responsable de empresa.
     * 14. Editar configuración de cliente.
     * 15. Actualizar configuración de cliente.
     * 16. Condiciones de pago de cliente.
     * 16.1. Condiciones de pago de proveedor.
     * 17. Actualizar condiciones de pago de cliente.
     * 18. Obtener formulario de presupuesto por defecto de cliente.
     * 19. Usuarios por empresa para DataTable.
     */
    
    private $option = 'empresas';
    private $module = 'companies';
    private $co_currency;
    
    public function __construct(){
        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('management_customers', ['only' => ['customers', 'createCustomer']]);
        $this->middleware('management_providers', ['only' => ['providers', 'createProvider']]);
        $this->middleware('customer_provider', ['only' => ['destroy', 'updateConditions']]);
        $this->middleware('customer_get', ['only' => ['editCustomer']]);
        $this->middleware('customer_post', ['only' => ['updateCustomerConfig']]);
        $this->middleware('role_company', ['only' => ['addManager']]);
    } 

    //0. Validator:
    protected function validator(array $data){
        //Validación update company:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'company_id' => 'required|integer',
                'business_relationship' => 'required',
                'nif' => 'required'
            ]); 

        //Validación agregar responsable:
        }elseif(isset($data['submit-add-responsable'])){
            return Validator::make($data, [
                'company_id' => 'required|integer',
                'name' => 'required|max:191',
                'surname' => 'required|max:191',
                'email' => 'required|string|email|max:191|unique:users,email',
                'role_id' => 'required|integer' 
            ],[
                'email.unique' => trans('textos.email_existe')
            ]); 

        //Validación update configuración cliente: 
        }elseif(isset($data['submit_update_config'])){
            return Validator::make($data, [
                'customer_id' => 'required|integer',
                'ba_id' => 'nullable|integer',
                'workplace_id' => 'nullable|integer',
                'cc_id' => 'nullable|integer',
                'discount' => 'nullable|numeric'
            ],[
                'discount.numeric' => trans('textos.valor_numerico')
            ]); 

        //Validación update configuración proveedor:
        }elseif(isset($data['submit_update_config_provider'])){
            return Validator::make($data, [
                'provider_id' => 'required|integer',
                'ac_id' => 'nullable|integer'
            ]);

        //Validación update conditions:
        }elseif(isset($data['submit_update_conditions'])){
            return Validator::make($data, [
                'pay_after_days' => 'nullable|numeric|max:999',
                'payday' => 'nullable|numeric|max:31',
                'billing_communication' => 'nullable|max:5000'
            ],[
                'pay_after_days.numeric' => trans('textos.dias_vencimiento_aviso'),
                'pay_after_days.max' => trans('textos.dias_vencimiento_aviso'),
                'payday.numeric' => trans('textos.dia_mes_aviso'),
                'payday.max' => trans('textos.dia_mes_aviso'),
            ]);
        } 
    }

    /**
     * 1. Listado de clientes.
     *
     * @return \Illuminate\Http\Response
     */
    public function customers(){
        $header = [
            'option' => 'empresas', 
            'suboption' => 'textos.clientes',
            'module' => $this->module,
            'slug' => 'customers'
        ];   

        $tabla = 'tblCustomers';

        //Roles básicos:
        //$basic_roles = $this->companyRoles();

        //Moneda:
        $currency = $this->co_currency;

        $concepts = [];

        return view('admin.customer_providers.index')->with(compact('header', 'tabla', 'currency', 'concepts'));   
    }  

    /**
     * 1.1. Listado de clientes para Datatable.
     */
    public function getCustomersDT(){
        $data = $this->getCompaniesData('customer_id', 'provider_id');

        $join = 'customers';

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name; 
        })
        ->addColumn('tradename', function($row){
            return $row->tradename; 
        })
        ->addColumn('nif', function($row){
            return $row->nif; 
        })
        ->addColumn('acciones', function($row) use($join){
            return view('admin.customer_providers.partials.dt-actions', compact('row', 'join'));    
        })
        ->make(true);
    }

    /**
     * 2. Listado de proveedores.
     *
     * @return \Illuminate\Http\Response
     */
    public function providers(){
        $header = [
            'option' => 'empresas', 
            'suboption' => 'textos.proveedores',
            'module' => $this->module,
            'slug' => 'providers'
        ];    

        $tabla = 'tblProviders';

        //Roles básicos:
        //$basic_roles = $this->companyRoles();

        //Moneda:
        $currency = $this->co_currency;

        //Conceptos contables:
        $concepts = AccountingConcept::where('company_id', session('currentCompany'))
        ->where('main', '0')
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.customer_providers.index')->with(compact('header', 'tabla', 'currency', 'concepts'));   
    }

    /**
     * 2.1. Listado de proveedores para Datatable.
     */
    public function getProvidersDT(){
        $data = $this->getCompaniesData('provider_id', 'customer_id');

        $join = 'providers';

        return DataTables::of($data)
        // ->addColumn('name', function($row){
        //     return $row->name; 
        // })
        // ->addColumn('tradename', function($row){
        //     return $row->tradename; 
        // })
        // ->addColumn('nif', function($row){
        //     return $row->nif; 
        // })
        ->addColumn('acciones', function($row) use($join){
            return view('admin.customer_providers.partials.dt-actions', compact('row', 'join'));    
        })
        ->make(true);
    }

    /**
     * 3. Formulario nuevo cliente.
     */
    public function createCustomer(){
        $header = [
            'option' => 'clientes', 
            'suboption' => 'textos.cliente_nuevo',
            'module' => $this->module,
            'slug' => 'customers'
        ];

        $accounts = Account::where('status', 1)->get();

        $companies = $this->getOtherCompanies(session('currentCompany'));

        $business_relationship = 'customers';
        $guardian = true;

        return view('admin.customer_providers.create')->with(compact('header', 'accounts', 'companies', 'business_relationship', 'guardian'));      
    }

    /**
     * 3.1. Editar cliente.
     */
    public function editCustomer(Company $company, $tab = false){
        $header = [
            'option' => 'clientes', 
            'suboption' => 'textos.cliente_editar',
            'module' => $this->module,
            'slug' => 'customers'
        ];

        $workplaces = Workplace::where('company_id', $company->id)->get();

        $areas = BusinessAreas::where('company_id', $company->id)->get();

        $conditions = CustomerProviders::where('provider_id', $company->id)
        ->where('customer_id', session('currentCompany'))
        ->first();

        $business_relationship = 'customers';
        $environment = 'customers';

        //Cuenta de la empresa:
        $company_account = CompanyAccounts::where('company_id', $company->id)
        ->where('status', 1)
        ->first();

        //Bancos:
        $banks = Bank::getBanks(true);

        //Cuentas bancarias de la empresa:
        $bank_accounts = BankAccounts::getBankAccounts($company->id);

        //Métodos de pago:
        $payment_methods = PaymentMethod::where('company_id', $company->id)
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.customer_providers.edit')->with(compact('header', 'company', 'tab', 'workplaces', 'areas', 'conditions', 'business_relationship', 'environment', 'company_account', 'banks', 'bank_accounts', 'payment_methods'));     
    }

    /**
     * 3.2. Mostrar ficha cliente o proveedor.
     */
    public function show(CustomerProviders $cp){
        //Discernir concepto a buscar:
        if($cp->provider_id == session('currentCompany')){
            $search_id = $cp->customer_id;
            $suboption_text = 'cliente_info';
            $origin = 'customers';
            $option_ = 'clientes';

        }elseif($cp->customer_id == session('currentCompany')){
            $search_id = $cp->provider_id;
            $suboption_text = 'proveedor_info';
            $origin = 'providers';
            $option_ = 'proveedores';
        }

        $header = [
            'option' => $option_, 
            'suboption' => 'textos.'.$suboption_text,
            'module' => $this->module,
            'slug' => $origin
        ];   

        $company = Company::find($search_id);

        $employees = Employee::select('users.id', 'users.name', 'users.surname', 'users.email', DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image'))
        ->join('users', 'employees.user_id', '=', 'users.id')
        ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        ->where('users.status', 1)
        ->where('employees.status', 1)
        ->get(); 

        //Configuración de la empresa mostrada:
        $settings = CompanySettings::select('public_info')->where('company_id', $search_id)->first();
        $public_info = $settings && $settings->public_info? unserialize($settings->public_info):[];

        $workplaces = Workplace::select('workplaces.id', 'workplaces.name', 'workplaces.featured', 'workplaces.logo', 'workplaces.address', 'workplaces.cp', 'workplaces.nif', 'workplaces.website', 'workplaces.description', 'towns.name AS Town', 'provinces.name AS Province')
        ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
        ->leftJoin('provinces', 'towns.province_id', '=', 'provinces.id')
        ->where('workplaces.company_id', $search_id)
        ->where('workplaces.status', 1)
        ->get();

        return view('admin.companies.show')->with(compact('header', 'origin', 'company', 'employees', 'public_info', 'workplaces'));
    }

    /**
     * 4. Formulario nuevo proveedor.
     */
    public function createProvider(){
        $header = [
            'option' => 'proveedores', 
            'suboption' => 'textos.proveedor_nuevo',
            'module' => $this->module,
            'slug' => 'providers'
        ];

        $accounts = Account::where('status', 1)->get(); 

        $companies = $this->getOtherCompanies(session('currentCompany'));

        $business_relationship = 'providers';
        $guardian = true;

        return view('admin.customer_providers.create')->with(compact('header', 'accounts', 'companies', 'business_relationship', 'guardian'));      
    }

    /**
     * 4.1. Editar proveedor.
     */
    public function editProvider(Company $company, $tab = false){
        $header = [
            'option' => 'proveedores', 
            'suboption' => 'textos.proveedor_editar',
            'module' => $this->module,
            'slug' => 'providers'
        ];

        $workplaces = Workplace::where('company_id', $company->id)->get();

        $areas = BusinessAreas::where('company_id', $company->id)->get();

        $conditions = CustomerProviders::where('provider_id', $company->id)
        ->where('customer_id', session('currentCompany'))
        ->first();

        $business_relationship = 'providers';
        $environment = 'providers';

        //Cuenta de la empresa:
        $company_account = CompanyAccounts::where('company_id', $company->id)
        ->where('status', 1)
        ->first();

        //Bancos:
        $banks = Bank::getBanks(true);

        //Cuentas bancarias de la empresa:
        $bank_accounts = BankAccounts::getBankAccounts($company->id);

        //Métodos de pago:
        $payment_methods = PaymentMethod::where('company_id', session('currentCompany'))
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.customer_providers.edit')->with(compact('header', 'company', 'tab', 'workplaces', 'areas', 'conditions', 'business_relationship', 'environment', 'company_account', 'banks', 'bank_accounts', 'payment_methods'));     
    }

    /**
     * 5. Vincular / desvincular cliente y proveedor.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $cp = CustomerProviders::find($id);
            $cp->status = $status;
            $cp->save();

            $resp = ($cp)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 6. Eliminar cliente o proveedor.
     *
     * @param  int  $cp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, CustomerProviders $cp){
        $cp->delete();

        $msg = $request->tipo == 'customers'? trans('textos.cliente_deleted'):trans('textos.proveedor_deleted');
        return redirect()->route($request->tipo.'.index')->with(compact('msg')); 
    }

    /**
     * 7. Obtener empresas distintas a la de sesión.
     *
     * @param  int  $id
     */
    private function getOtherCompanies($id){
        $data = Company::select('id', 'name')
        ->where('id', '!=', $id)
        ->orWhereNull('id')
        ->orderBy('name', 'ASC')
        ->get();

        return $data;
    }

    /**
     * 8. Obtener info de empresas por criterio.
     */
    private function getCompaniesData($join, $condition){
        $data = CustomerProviders::select('customer_providers.id AS cp_id', 'customer_providers.ba_id', 'customer_providers.workplace_id', 'customer_providers.cc_id', 'customer_providers.discount', 'customer_providers.credit_risk', 'customer_providers.budget_access', 'customer_providers.syncro_invoice', 'customer_providers.status AS cp_status', 'customer_providers.accounting_concept_id', 'customer_providers.expense_account_id', 'customer_providers.bank_account_id', 'companies.id', 'companies.name', 'companies.tradename', 'companies.logo', 'companies.nif', 'company_accounts.guardian', 'company_accounts.status')
        ->join('companies', 'customer_providers.'.$join, '=', 'companies.id')
        ->leftJoin('company_accounts', 'companies.id', '=', 'company_accounts.company_id')
        ->where('companies.id', '!=', session('currentCompany'))
        ->where('customer_providers.'.$condition, session('currentCompany'));

        //Array de empresas asociadas al usuario en sesión:
        $companies = [];
        foreach(session('companies') as $co){
            array_push($companies, $co->id);
        }

        return $data;
    }

    /**
     * 9. Formulario importación .xls.
     */
    public function importForm(){
        $pre_segments = explode('admin/', url()->current());
        $segments = explode('/', $pre_segments[1]);
        $business_relationship = $segments[0];
        $arrErrors = false;

        //Comprobamos si existen registros .xls pendientes de tratar:
        $data = TempImportCompanies::where('user_id', Auth::user()->id)
        ->orderBy('id', 'ASC')
        ->get();

        if($data->count()){
            $arrErrors = $this->setXlsRegisters($data, $business_relationship);
        }

        //Eliminamos registros pendientes de la tabla temporal:
        TempImportCompanies::where('user_id', Auth::user()->id)->delete();

        if($business_relationship == 'customers'){
            $texto = 'importar_clientes';
        }elseif($business_relationship == 'providers'){
            $texto = 'importar_proveedores';
        }
        
        $xls_errors = $arrErrors? $arrErrors['xls_errors']:[];
        $withXlsErrors = $arrErrors? $arrErrors['withXlsErrors']:false;

        $header = [
            'option' => 'empresas', 
            'suboption' => 'textos.'.$texto,
            'module' => $this->module,
            'slug' => $business_relationship
        ];
       
        return view('admin.customer_providers.import')->with(compact('header', 'business_relationship', 'xls_errors', 'withXlsErrors')); 
    }

    /**
     * 10. Procesando registro de importación .xls.
     */
    public function setXlsRegisters($data, $environment){
        $xls_errors = [];
        $withXlsErrors = false;
        $i = 2;     //La fila 1 corresponde a las cabeceras de las columnas.
        $paymentMethods = helpers::paymentMethods();

        foreach($data as $r){
            $xls_errors[$i] = false;

            //Validamos NIF:
            if($r->nif){
                $nif = $r->nif;

                //Comprobamos si existe la empresa:
                $company = Company::where('nif', $r->nif)->first();

                if(!$company){
                    //Validamos nombre empresa:
                    if($r->name){
                        $name = $r->name;
                        $slug = Str::slug($name);
                    }else{
                        $name = false;
                        $name = $xls_errors[$i] .= trans('textos.razon_social').': '.trans('textos.razon_social_obligatorio').' ';    
                    }   
                    
                    //Creamos empresa:
                    if($name){
                        $company = Company::create([
                            'name' => $name,
                            'slug' => $slug,
                            'tradename' => $r->tradename,
                            'nif' => $nif    
                        ]);
                    }
                }

                //Una vez creada la empresa:
                if($company){
                    //Centro de trabajo:
                    if($r->town){
                        //Obtenemos town_id:
                        $slug_town = Str::slug($r->town);
                        $town = Town::select('id')->where('slug', $slug_town)->first();

                        if($town){
                            //Verificamos que no exista el centro de trabajo:
                            $workplace = Workplace::select('id')
                            ->where('company_id', $company->id)
                            ->where('town_id', $town->id)
                            ->where('address', $r->address)
                            ->first();

                            if(!$workplace){
                                $workplace = new Workplace();
                                $workplace->company_id = $company->id;
                                $workplace->name = trans('textos.centro_trabajo');
                                $workplace->slug = Str::slug(trans('textos.centro_trabajo'));
                                $workplace->featured = 1;
                                $workplace->address = $r->address;
                                $workplace->cp = $r->cp;
                                $workplace->town_id = $town->id;
                                $workplace->save();
                            }
                        }else{
                            $xls_errors[$i] .= $r->name.', '.ucfirst(trans('textos.poblacion')).': '.trans('textos.poblacion_no_registrada').' ';
                            $workplace = false;    
                        }

                    }else{
                        $xls_errors[$i] .= $r->name.', '.ucfirst(trans('textos.poblacion')).': '.trans('textos.poblacion_obligatoria').' ';
                        $workplace = false;
                    }

                    $workplace_id = $workplace? $workplace->id:NULL;


                    //Vínculo cliente - proveedor:
                    if($environment == 'customers'){
                        $customerId = $company->id;
                        $providerId = session('currentCompany');
                    }else{
                        $customerId = session('currentCompany');
                        $providerId = $company->id;
                    }

                    //Verificamos si existe previamente la relación:
                    $customer_provider = CustomerProviders::where('customer_id', $customerId)
                    ->where('provider_id', $providerId)
                    ->first();

                    if(!$customer_provider){
                        //Concepto contable de la empresa en session. Primero obtenemos concepto general, y posteriormente cuenta de gastos, pues el término de cuenta de gastos puede repetirse entre distintos conceptos generales:
                        $general_concept = AccountingConcept::select('id')
                        ->where('company_id', session('currentCompany'))
                        ->where('name', $r->concepto_general)
                        ->where('main', '0')
                        ->first();

                        $concept = false;
                        if($general_concept){
                            $concept = AccountingConcept::select('id')
                            ->where('company_id', session('currentCompany'))
                            ->where('name', $r->cuenta_gastos)
                            ->where('main', $general_concept->id)
                            ->first();
                        }

                        //Método de pago:
                        $payment_method = $r->payment_method? array_search($r->payment_method, $paymentMethods):NULL;

                        $cu_pr = new CustomerProviders(); 
                        $cu_pr->customer_id = $customerId;
                        $cu_pr->provider_id = $providerId;
                        $cu_pr->workplace_id = $workplace_id;
                        $cu_pr->accounting_concept_id = $concept? $concept->id:NULL;
                        $cu_pr->payment_method = $payment_method;

                        //if($environment == 'customers'){
                            $cu_pr->pay_after_days = $r->pay_after_days;
                            $cu_pr->payday = $r->payday;
                            $cu_pr->billing_comunication = $r->billing_comunication;
                        //}
                        $cu_pr->save();
                    }

                    //Cuenta bancaria:
                    if($r->iban){
                        //Extraemos identificador del banco del IBAN:
                        $bank_code = substr($r->iban, 4, 4);
                        $bank = Bank::select('id')->where('supervisor_code', $bank_code)->first();

                        if($bank){
                            //Comprobamos que no exista la cuenta previamente:
                            $account = BankAccounts::select('id')
                            ->where('company_id', $company->id)
                            ->where('account', $r->iban)
                            ->where('bank_id', $bank->id)
                            ->first();

                            if(!$account){
                                $ba = new BankAccounts();
                                $ba->company_id = $company->id;
                                $ba->bank_id = $bank->id;
                                $ba->account = $r->iban;
                                $ba->featured = true;
                                $ba->status = true;
                                $ba->save();
                            }
                        }
                    }
                }

            }else{
                $nif = false;
                $xls_errors[$i] .= $r->name.', NIF: '.trans('textos.nif_obligatorio').' ';
            }

            if($xls_errors[$i]) $withXlsErrors = true;
            
            $i++;
        }  

        return ['xls_errors' => $xls_errors, 'withXlsErrors' => $withXlsErrors];
    }

    /**
     * 11. Exportar clientes a otras empresas vinculadas.
     */
    public function exportCustomers(Request $request){
        if(is_array($request->companies) && count($request->companies) > 0){  
            $data = CustomerProviders::where('customer_providers.provider_id', session('currentCompany'))
            ->get();  

            if($data->count() > 0){
                foreach($request->companies as $co){
                    foreach($data as $row){
                        //Comprobamos que no exista el registro:
                        $exist = CustomerProviders::select('id')
                        ->where('customer_id', $row->customer_id)
                        ->where('provider_id', $co)
                        ->first();

                        if(!$exist){
                            $item = new CustomerProviders();
                            $item->customer_id = $row->customer_id;
                            $item->provider_id = $co;
                            $item->pay_after_days = $row->pay_after_days;
                            $item->payday = $row->payday;
                            $item->ba_id = $row->ba_id;
                            $item->workplace_id = $row->workplace_id;
                            $item->cc_id = $row->cc_id;
                            $item->discount = $row->discount;
                            $item->billing_comunication = $row->billing_comunication;
                            $item->status = $row->status;
                            $item->save();
                        }
                    }
                }
                $msg = trans('textos.exportacion_ok');
                return redirect()->route('customers.index')->with(compact('msg'));

            }else{
                $alert = trans('textos.exportacion_vacia');
            }

        }else{
            $alert = trans('textos.exportacion_vacia');
        }

        return redirect()->route('customers.index')->with(compact('alert'));
    }

    /**
     * 12. Exportar proveedores a otras empresas vinculadas.
     */
    public function exportProviders(Request $request){
        if(is_array($request->companies) && count($request->companies) > 0){  
            $data = CustomerProviders::where('customer_providers.customer_id', session('currentCompany'))
            ->get();  

            if($data->count() > 0){
                foreach($request->companies as $co){
                    foreach($data as $row){
                        //Comprobamos que no exista el registro:
                        $exist = CustomerProviders::select('id')
                        ->where('customer_id', $co)
                        ->where('provider_id', $row->provider_id)
                        ->first();

                        if(!$exist){
                            $item = new CustomerProviders();
                            $item->customer_id = $co;
                            $item->provider_id = $row->provider_id;
                            $item->pay_after_days = $row->pay_after_days;
                            $item->payday = $row->payday;
                            $item->ba_id = $row->ba_id;
                            $item->workplace_id = $row->workplace_id;
                            $item->cc_id = $row->cc_id;
                            $item->discount = $row->discount;
                            $item->billing_comunication = $row->billing_comunication;
                            $item->status = $row->status;
                            $item->save();
                        }
                    }
                }

                $msg = trans('textos.exportacion_ok');
                return redirect()->route('providers.index')->with(compact('msg'));

            }else{
                $alert = trans('textos.exportacion_vacia');
            }

        }else{
            $alert = trans('textos.exportacion_vacia');
        }

        return redirect()->route('providers.index')->with(compact('alert'));
    }

    /**
     * 13. Añadir responsable de empresa.
     */
    public function addManager(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-manager');

            return redirect('admin/'.$request->return)
                ->withErrors($validator)
                ->withInput();
        }

        //Comprobamos si existe el usuario, y en caso de existir se vincula a la empresa:
        $user = User::where('email', $request->input('email'))->first();

        if(!$user){
            //Guardamos datos de usuario:
            $user = new User();
            $user->name = $request->input('name');
            $user->surname = $request->input('surname');
            $user->email = $request->input('email');

            //Password:
            if($request->role_id){
                $random_password = Str::random(8);  
                $user->password = bcrypt($random_password);  
            }

            $user->isAdmin = $request->role_id? true:false;
            $user->status = $request->role_id? 1:0;

            $user->remember_token = $request->input('_token');
            $user->save();

            //Guardamos rol:
            $role = $request->role_id? $request->role_id:config('constants.ROLE_ID_');
            $user->assignRole($role);
        }

        //Vinculamos usuario a empresa, comprobando primero si existe:
        $exist = UserCompanies::select('id')
        ->where('user_id', $user->id)
        ->where('company_id', $request->company_id)
        ->first();
        
        if(!$exist){
            $uc = new UserCompanies();
            $uc->user_id = $user->id;
            $uc->company_id = $request->company_id;
            $uc->save(); 
        }

        //Envío de password:
        if($request->role_id){
            $emailFrom = config('constants.EMAIL_');
            $emailTo = $user->email;
            $data['usuario'] = $user->name.' '.$user->surname;
            $data['password'] = $random_password;
            $company = Company::find($request->company_id);
            $data['company'] = $company;

            Mail::send('emails.send-user-password', $data, function($message) use($emailFrom, $company, $emailTo){
                $message->from($emailFrom, $company->name);
                $message->to($emailTo);
                $message->subject(trans('textos.contrasena_envio')); 
            }); 
        }

        $msg = trans('textos.responsable_agregado');
        return redirect()->route($request->return.'.index')->with(compact('msg'));
    }

    /**
     * 14. Editar configuración de cliente.
     *
     * @param  obj $company
     * @return \Illuminate\Http\Response
     */
    public function editCustomerConfig(Request $request, Company $company, $accounting_concept_id = false){
        if($request->ajax()){
            $areas = BusinessAreas::select('id', 'name')
            ->where('company_id', $company->id)
            ->where('status', 1)
            ->get();

            $workplaces = Workplace::select('id', 'name')
            ->where('company_id', $company->id)
            ->where('status', 1)
            ->get();

            $centers = CostCenters::select('id', 'name')
            ->where('company_id', $company->id)
            ->where('status', 1)
            ->get();

            //Devolvemos las cuentas de gastos del concepto general:
            $concepts = false;
            if($accounting_concept_id){
                $concepts = AccountingConcept::select('id', 'name')
                ->where('main', $accounting_concept_id)
                ->where('status', 1)
                ->get();
            }

            $data['company'] = $company->name;
            $data['areas'] = $areas;
            $data['workplaces'] = $workplaces;
            $data['centers'] = $centers;
            $data['concepts'] = $concepts;

            return response()->json($data);
        }
    }

    /**
     * 15. Actualizar configuración de cliente.
     */
    public function updateCustomerConfig(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-config');

            return redirect('/admin/customers')
                ->withErrors($validator)
                ->withInput();
        }

        $cp = CustomerProviders::where('customer_id', $request->customer_id)
        ->where('provider_id', session('currentCompany'))
        ->first();

        $cp->ba_id = $request->ba_id;
        $cp->workplace_id = $request->workplace_id;
        $cp->cc_id = $request->cc_id;
        $cp->discount = $request->discount;
        $cp->credit_risk = $request->credit_risk;
        $cp->budget_access = $request->budget_access? true:false;
        $cp->syncro_invoice = $request->syncro_invoice? true:false;
        $cp->save();

        $msg = trans('textos.cliente_updated');
        return redirect()->route('customers.index')->with(compact('msg'));
    }

    /**
     * 15.1. Actualizar configuración de proveedor.
     */
    public function updateProviderConfig(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-config-provider');

            return redirect('/admin/providers')
                ->withErrors($validator)
                ->withInput();
        }    

        $cp = CustomerProviders::where('customer_id', session('currentCompany'))
        ->where('provider_id', $request->provider_id)
        ->first();

        $cp->accounting_concept_id = $request->ac_id;
        $cp->expense_account_id = $request->ea_id;
        $cp->bank_account_id = $request->bank_account_id;
        $cp->save();

        $msg = trans('textos.proveedor_updated');
        return redirect()->route('providers.index')->with(compact('msg'));
    }

    /**
     * 16. Condiciones de pago de cliente.
     */
    public function payConditions(Company $company){
        $header = [
            'option' => 'clientes', 
            'suboption' => 'textos.condiciones_pago',
            'module' => $this->module,
            'slug' => 'customers'
        ];

        $environment = 'customers';

        //Cuenta de la empresa:
        $company_account = CompanyAccounts::where('company_id', $company->id)
        ->where('status', 1)
        ->first();

        $conditions = CustomerProviders::where('customer_id', $company->id)
        ->where('provider_id', session('currentCompany'))
        ->first();

        //Areas de negocio:
        $areas = BusinessAreas::where('company_id', $company->id)->get();

        //Métodos de pago:
        $payment_methods = PaymentMethod::where('company_id', $company->id)
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.customer_providers.pay-conditions')->with(compact('header', 'company', 'environment', 'company_account', 'conditions', 'areas', 'payment_methods')); 
    }

    /**
     * 16.1. Condiciones de pago de proveedor.
     */
    public function payConditionsProvider(Company $company){
        $header = [
            'option' => 'proveedores', 
            'suboption' => 'textos.condiciones_pago',
            'module' => $this->module,
            'slug' => 'providers'
        ];

        $environment = 'providers';

        //Cuenta de la empresa:
        $company_account = CompanyAccounts::where('company_id', $company->id)
        ->where('status', 1)
        ->first();

        $conditions = CustomerProviders::where('provider_id', $company->id)
        ->where('customer_id', session('currentCompany'))
        ->first();

        //Areas de negocio:
        $areas = BusinessAreas::where('company_id', $company->id)->get();

        //Métodos de pago:
        $payment_methods = PaymentMethod::where('company_id', session('currentCompany'))
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.customer_providers.pay-conditions')->with(compact('header', 'company', 'environment', 'company_account', 'conditions', 'areas', 'payment_methods')); 
    }

    /**
     * 17. Actualizar condiciones de pago de cliente.
     */
    public function updateConditions(Request $request, CustomerProviders $cp){
        $validator = $this->validator($request->all());
        if($validator->fails()){    
            $segment = $request->environment == 'customers'? 'pay-conditions':'pay-conditions-provider';      

            return redirect('/admin/'.$request->environment.'/'.$request->company_id.'/'.$segment)
                ->withErrors($validator)
                ->withInput();
        }

        if($request->pay_after_days){
            $cp->pay_after_days = $request->pay_after_days;
        }
        if($request->payday){
            $cp->payday = $request->payday;
        }
        if($request->billing_comunication){
            $cp->billing_comunication = $request->billing_comunication;
        }
        if($request->payment_method){
            $cp->payment_method_id = $request->payment_method;
        }
        $cp->save();

        if($request->environment == 'customers'){
            if($request->return == 'edit'){
                $ruta = 'customers.edit';
            }else{
                $ruta = 'customers.pay-conditions';
            }
        }else{
            if($request->return == 'edit'){
                $ruta = 'providers.edit';
            }else{
                $ruta = 'providers.pay-conditions-provider';
            }
        }

        //$ruta = $request->environment == 'customers'? 'customers.pay-conditions':'providers.pay-conditions-provider';

        $msg = trans('textos.cliente_updated');
        return redirect()->route($ruta, $request->company_id)->with(compact('msg'));
    }

    /**
     * 18. Obtener formulario de presupuesto por defecto de cliente.
     */
    public function ownBudgetForm(Request $request, $customer_id, $provider_id = false){
        if($request->ajax()){ 
            $prov_id = $provider_id? $provider_id:session('currentCompany');

            $data = CustomerProviders::select('budget_form_id')
            ->where('provider_id', $prov_id)
            ->where('customer_id', $customer_id)
            ->first();

            return response()->json($data);
        }
    }

    /**
     * 19. Usuarios por empresa para DataTable.
     */
    public function getUsersDT($company_id){
        $data = User::with('phones')->select('users.*', 'user_companies.categories',
            DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image')
            )
        ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
        ->where('user_companies.company_id', $company_id)
        ->groupBy('users.id')
        ->orderBy('users.name');

        return DataTables::of($data)
        ->addColumn('user', function($row){
            return $row->name.' '.$row->surname;
        })
        ->addColumn('email', function($row){
             return '<a target="_blank" href="mailto:'.$row->email.'">'.$row->email.'</a>';
        })
        ->addColumn('phones', function($row){
            $phones = '';
            $data_phones = $row->phones;
            $i = 0;
            if($data_phones->count() > 0){
                foreach($data_phones as $p){
                    if($i > 0){
                        $phones .= ' - ';
                    }
                    $phones .= $p->phone_number;
                    $i++;
                }
            }
            return $phones;
        })
        ->addColumn('categories', function($row){
            $categories = '';
            if($row->categories){
                $arr = explode(',', $row->categories);
                $obj_categories = Category::whereIn('id', $arr)
                ->pluck('name')
                ->toArray();

                $categories = implode(', ', $obj_categories);
            }

            return $categories;
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="'.asset('img/users/'.$row->Image).'" class="img-circle" width="36">';
            }else{
                return helpers::setAvatar($row->name, $row->surname);
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.customer_providers.partials.dt-actions-users', compact('row'));
        })
        ->rawColumns(['email', 'image'])
        ->make(true);
    }

}
