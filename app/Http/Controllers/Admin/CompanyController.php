<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;

//Models:
use App\Models\Account;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\CompanyModules;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\TempImportCompanies;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\Workplace;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

//Import XLS:
use App\Imports\CompaniesImport;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller{
    /**
     * 1. Listado de empresas.
     * 1.1. Listado de empresas para DataTable.
     * 2. Formulario nueva empresa.
     * 3. Guardar nueva empresa.
     * 3.1. Proceso insert nueva empresa.
     * 3.2. Nueva empresa desde Gastos (proveedor).
     * 4. Editar empresa.
     * 5. Actualizar empresa.
     * 6. Eliminar empresa.
     * 7. Actualizar estado.
     * 8. Subir logo.
     * 9. Eliminar logo.
     * 10. Seleccionar empresa para la sesión.
     * 11. Seleccionar empresa para la sesión por Post.
     * 12. Mostrar datos de empresa.
     * 12.1. Obtener usuarios de la empresa.
     * 13. Importar empresas desde .xls.
     * 14. Mostrar ficha de empresa.
     * 15. Establecer empresa matriz.
     * 16. Desvincular de empresa matriz.
     * 17. Directorio por sectores.
     * 18. Roles por empresa.
     * 19. Empresas para autocomplete.
     * 20. Proveedores para autocomplete.
     */

    private $option = 'empresas';
    private $module = 'companies';
    private $business_relationship = 'companies';
    private $languages;

    public function __construct(Excel $excel){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('company_get', ['only' => ['edit', 'update', 'selectCompany', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required_without:company_id|max:255',
                'tradename' => 'nullable|max:255',
                'nif' => 'required_without:company_id|validateNif|max:15|unique:companies,nif,NULL,id,deleted_at,NULL',
                'account_id' => 'nullable|numeric',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:500'
            ],[
                'nif.unique' => trans('textos.nif_existe')
            ]);

        //Validación save_from_expense:
        }elseif(isset($data['submit_save_from_expense'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'tradename' => 'nullable|max:255',
                'nif' => 'required|validateNif|max:15|'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => "required|string|max:255",
                'tradename' => 'nullable|max:255',
                'nif' => "required|string|max:15|validateNif|unique:companies,nif,".$data['id'].",id,deleted_at,NULL",
                'ccc' => "max:50"
            ],[
                'nif.unique' => trans('textos.nif_existe')
            ]);
        }
    }

    /**
     * 1. Listado de empresas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.empresas_mis',
            'module' => $this->module,
            'slug' => 'companies'
        ];

        return view('admin.companies.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de empresas para DataTable.
     */
    public function getCompaniesDT(){
        $data = Company::select('companies.*')
        ->join('user_companies', 'companies.id', '=', 'user_companies.company_id')
        ->where('user_companies.user_id', Auth::user()->id);

        return DataTables::of($data)
        ->addColumn('alta', function($row){
            $alta = $row->created_at->format($this->languages[session()->get('locale')][4]);
            return $alta;
        })
        ->addColumn('ute', function($row){
            if($row->is_ute){
                return '<span style="display: none">ute</span><i class="fa fa-check text-success"></i>';
            }else{
                return '';
            }
        })
        ->addColumn('logo', function($row){
            if($row->logo){
                return '<img src="/img/companies/'.$row->logo.'" width="50">';
            }else{
                return '<i class="fa fa-building icono"></i>';
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.companies.partials.dt-actions', compact('row'));
        })
        ->rawColumns(['ute', 'logo'])
        ->make(true);
    }

    /**
     * 2. Formulario nueva empresa.
     */
    public function create(){
        $accounts = Account::where('status', 1)->get();
        $guardian = true;
        $business_relationship = $this->business_relationship;

        //En función del estado del usuario se redirige a frontend o backend:
        if(Auth::user()->status == 1){
            $header = [
                'option' => $this->option,
                'suboption' => 'textos.empresa_nueva',
                'module' => $this->module,
                'slug' => 'companies-create'
            ];

            $languages = $this->languages;

            return view('admin.companies.create')->with(compact('header', 'accounts', 'guardian', 'business_relationship', 'languages'));

        }else{
            return view('web.company-register')->with(compact('accounts', 'guardian', 'business_relationship'));
        }
    }

    /**
     * 3. Guardar nueva empresa.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/'.$request->business_relationship.'/create')
            ->withErrors($validator)
            ->withInput();
        }

        //Si se ha seleccionado una empresa ya existente:
        if($request->company_id){
            //Comprobamos que no exista previamente la relación cliente-proveedor:
            if($request->input('business_relationship') == 'customers'){
                $customer_id = $request->company_id;
                $provider_id = session('currentCompany');

            }else{
                $customer_id = session('currentCompany');
                $provider_id = $request->company_id;
            }

            $exist = CustomerProviders::where('customer_id', $customer_id)
                ->where('provider_id', $provider_id)
                ->first();

            if($exist){
                $alert = trans('textos.cliente_proveedor_existe_error');
                if($request->input('business_relationship') == 'customers'){
                    return redirect()->route('customers.index')->with(compact('alert'));
                }else{
                    return redirect()->route('providers.index')->with(compact('alert'));
                }
            }

            $cp = new CustomerProviders();
            $cp->customer_id = $customer_id;
            $cp->provider_id = $provider_id;
            $cp->save();

        //Nueva empresa:
        }else{
            $this->saveCompany($request);
        }

        //Rehacemos session de empresas vinculadas al usuario.
        //UserCompanies::userCompanies();

        $msg = trans('textos.empresa_creado_ok');

        // if($request->environment){
        //     //Retorno de cliente o proveedor:
        //     if($request->input('business_relationship')){
        //         if($request->input('business_relationship') == 'customers'){
        //             $msg = trans('textos.cliente_creado_ok');
        //             return redirect()->route('customers.index')->with(compact('msg'));
        //         }else{
        //             $msg = trans('textos.proveedor_creado_ok');
        //             return redirect()->route('providers.index')->with(compact('msg'));
        //         }

        //     }else{
        //         return redirect()->route('companies.edit', $company->id)->with(compact('msg'));
        //     }
        // }else{
        //     return redirect()->route('companies.index')->with(compact('msg'));
        // }

        return redirect()->route($request->business_relationship.'.index')->with(compact('msg'));
    }

    /**
     * 3.1. Proceso insert nueva empresa.
     */
    private function saveCompany($request){
        //Slug:
        $slug = Str::slug($request->name);

        //Subir logo:
        $fileName = $this->loadLogo($request, 'companies');

        //Fechas UTE:
        $ute_start_at = NULL;
        $ute_finish_at = NULL;
        if($request->is_ute){
            $ute_start_at = $request->input('ute_start_at') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('ute_start_at')):$request->input('ute_start_at'):NULL;
            $ute_finish_at = $request->input('ute_finish_at') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('ute_finish_at')):$request->input('ute_finish_at'):NULL;
        }
        $nif = $request->nif? helpers::cleanNif($request->nif):NULL;

        $company = Company::create([
            'name' => $request->name,
            'slug' => $slug,
            'tradename' => $request->tradename,
            'logo' => $fileName,
            'nif' => $nif,
            'is_ute' => $request->is_ute? true:false,
            'ute_start_at' => $ute_start_at,
            'ute_finish_at' => $ute_finish_at
        ]);

        //Indicador de empresa matriz. Usualmente es el mismo id, salvo para aquellas empresas dependientes de una empresa matriz:
        $company->mother_co = $company->id;
        $company->save();

        //Roles de la empresa:
        $company->setCompanyRoles(true);

        //Creamos centro de trabajo por defecto:
        $wp = new Workplace();
        $wp->company_id = $company->id;
        $wp->name = ucfirst(trans('textos.sede'));
        $wp->slug = Str::slug(trans('textos.sede'));
        $wp->featured = 1;
        $wp->save();

        //Vinculamos usuario a empresa:
        if($request->business_relationship == 'companies'){
            $uc = new UserCompanies();
            $uc->user_id = Auth::user()->id;
            $uc->company_id = $company->id;
            $uc->save();

            //Si proviene de frontend:
            if(!$request->environment){
                //Actualizamos estado de usuario:
                $user = User::find(Auth::user()->id);
                $user->isAdmin = 1;
                $user->status = 1;
                $user->save();

                //Guardamos rol de manager, que obtenemos de los roles recién creados:
                $role = Role::where('name', $company->id.'/manager')
                ->where('company_id', $company->id)
                ->first();

                //$role = $request->environment? $request->input('roles'):config('constants.ROLE_COMPANY_MANAGER_');
                $user->assignRole($role);
            }

            //Pasamos empresa a la sesión de empresas:
            \Session::push('companies', $company);

            //Empresa actual:
            if(!session('currentCompany')){
                session(['currentCompany' => $company->id]);
            }

        //Vinculamos como cliente o proveedor de la empresa con sesión, y no se vincula usuario:
        }else{
            if($request->business_relationship == 'customers'){
                $customer_id = $company->id;
                $provider_id = session('currentCompany');
            }else{
                $customer_id = session('currentCompany');
                $provider_id = $company->id;
            }

            $cp = new CustomerProviders();
            $cp->customer_id = $customer_id;
            $cp->provider_id = $provider_id;
            $cp->save();
        }

        //Info de la cuenta suscrita:
        $account = $request->account_id? Account::find($request->account_id):false;

        if($account){
            $hoy = date('Y-m-d');
            $duration = $account->duration? $account->duration:config('constants.ACCOUNT_DURATION_');

            //Fecha de finalización:
            $end_date = date('Y-m-d', strtotime($hoy.'+ '.$duration.' days'));

            //Abrimos cuenta de la empresa:
            $ca = new CompanyAccounts();
            $ca->company_id = $company->id;
            $ca->guardian = $request->guardian? session('currentCompany'):NULL;
            $ca->account_id = $account->id;
            $ca->start_date = $hoy;
            $ca->end_date = $end_date;
            $ca->price = $account->rate;
            $ca->status = '0';
            $ca->save();
        }

        return $company;
    }

    /**
     * 3.2. Nueva empresa desde Gastos (proveedor).
     */
    public function storeFromExpense(Request $request){
        if($request->ajax()){
            // $validator = $this->validator($request->all());
            // if($validator->fails()){
            //     $validator->getMessageBag()->add('accion', 'modal-add-provider');

            //     return redirect('admin/expenses/create')
            //     ->withErrors($validator)
            //     ->withInput();
            // }

            //Comprobamos por NIF si la empresa ya existe:
            $company = Company::select('id', 'name')->where('nif', $request->nif)->first();

            //Si existe vinculamos cliente y proveedor:
            if($company){
                //Comprobamos que no exista previamente la relación:
                $exist = CustomerProviders::select('id')
                ->where('customer_id', session('currentCompany'))
                ->where('provider_id', $company->id)
                ->first();

                if(!$exist){
                    $cp = new CustomerProviders();
                    $cp->customer_id = session('currentCompany');
                    $cp->provider_id = $company->id;
                    $cp->save();
                }

            //Si no existe, la creamos:
            }else{
                $company = $this->saveCompany($request);
            }

            return response()->json($company);
        }
    }

    /**
     * 4. Editar empresa.
     */
    public function edit(Company $company, $tab = false){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.empresa_editar',
            'module' => $this->module,
            'slug' => 'companies'
        ];

        //Página sin cambio de empresa:
        $noSelectCompany = true;

        //Pasamos a session la empresa editada:
        //session(['currentCompany' => $company->id]);

        $business_relationship = $this->business_relationship;

        //Empresa matriz:
        $mother = false;
        if($company->id != $company->mother_co){
            $mother = Company::select('id', 'name', 'tradename')
            ->find($company->mother_co);
        }

        //Empresas matriz:
        $mother_companies = Company::where('is_ute', '0')
        ->whereRaw('id = mother_co')
        ->where('status', 1)
        ->where('id', '!=', $company->id)
        ->pluck('name', 'id')
        ->toArray();

        $languages = $this->languages;

        return view('admin.companies.edit', compact('header', 'company', 'tab', 'noSelectCompany', 'business_relationship', 'mother', 'mother_companies', 'languages'));
    }

    /**
     * 5. Actualizar empresa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/companies/'.$company->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Subir logo:
        $fileName = $this->loadLogo($request, 'companies');

        $nif = $request->input('nif')? helpers::cleanNif($request->input('nif')):NULL;

        $company->name = $request->input('name');
        $company->slug = Str::slug($request->input('name'));
        $company->tradename = $request->input('tradename');
        $company->acronym = $request->input('acronym');
        if($fileName){
            $company->logo = $fileName;
        }
        $company->nif = $nif;
        $company->ccc = $request->input('ccc');

        //UTE:
        if($company->is_ute){
            $ute_start_at = $request->input('ute_start_at') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('ute_start_at')):$request->input('ute_start_at'):NULL;
            $ute_finish_at = $request->input('ute_finish_at') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('ute_finish_at')):$request->input('ute_finish_at'):NULL;

            $company->ute_start_at = $ute_start_at;
            $company->ute_finish_at = $ute_finish_at;
        }

        $company->save();

        //Modificamos datos en la sesión de empresas:
        if($request->business_relationship == 'companies'){
            session()->forget('companies');
            //Empresas vinculadas al usuario:
            $companies = Auth::user()->companies();
            foreach($companies as $row){
                \Session::push('companies', $row);
            }
        }

        $msg = trans('textos.empresa_updated');
        return redirect()->route($request->business_relationship.'.edit', $company->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar empresa.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company){
        $company_id = $company->id;
        File::delete(public_path().'/img/companies/'.$company->logo);

        $company->delete();

        //Eliminamos empresa de la sesión:
        $companies = session('companies');
        session()->forget('companies');

        //Generamos nueva sesión de empresas:
        foreach($companies as $c){
            if($c->id != $company->id){
                \Session::push('companies', $c);
            }
        }

        $msg = trans('textos.empresa_deleted');
        return redirect()->route('companies.index')->with(compact('msg'));
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){
            $company = Company::find($id);
            $company->status = $status;
            $company->save();

            UserCompanies::userCompanies();

            $resp = ($company)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 8. Subir logo.
     *
     * @param  obj $request
     * @return \Illuminate\Http\Response
     */
    // public function loadLogo($request){
    //     if($request->hasFile('logo')){
    //         $file = $request->file('logo');
    //         $path = public_path().'/img/companies';
    //         $fileName = uniqid().$file->getClientOriginalName();
    //         $moved = $file->move($path, $fileName);

    //         //si el archivo ha sido guardado correctamente:
    //         if($moved){
    //             return $fileName;
    //         }else{
    //             return false;
    //         }
    //     }
    // }

    /**
     * 9. Eliminar logo.
     *
     * @param  obj $request string $logo
     * @return \Illuminate\Http\Response
     */
    public function deleteLogo(Request $request, $logo, $business_relationship = false){
        if($logo != ''){
            $company = Company::select('id')->where('logo', $logo)->first();

            if(!$company){
                return redirect('/');
                exit;
            }

            File::delete(public_path().'/img/companies/'.$logo);

            $company->logo = NULL;
            $company->save();

            $business_relationship = $business_relationship? $business_relationship:'companies';

            $msg = trans('textos.imagen_deleted');
            return redirect()->route($business_relationship.'.edit', $company->id)->with(compact('msg'));

        }else{
            return redirect('/');
        }
    }

    /**
     * 10. Seleccionar empresa para la sesión.
     *
     * @param \App\Models\Company $company
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function selectCompany(Company $company){
        session(['currentCompany' => $company->id]);

        $prev_url = URL::previous();

        //Módulos de la empresa:
        $this->setCompanyModules($company->id);

        //Configuración de la empresa:
        $settings = CompanySettings::where('company_id', $company->id)->first();
        session(['companySettings' => $settings]);

        //Regresamos a la página en que se hallaba:
        return redirect($prev_url);
    }

    /**
     * 11. Seleccionar empresa para la sesión por Post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectCompanyPost(Request $request){
        $validator = Validator::make($request->all(), [
            'company' => 'required|numeric'
        ]);
        if($validator->fails()){
            return redirect('admin')
                ->withErrors($validator)
                ->withInput();
        }

        session(['currentCompany' => $request->company]);

        //Módulos de la empresa:
        $this->setCompanyModules($request->company);

        //Configuración de la empresa:
        $settings = CompanySettings::where('company_id', $request->company)->first();
        session(['companySettings' => $settings]);

        return redirect()->route('dashboard');
    }

    /**
     * 13. Importar empresas desde .xls.
     */
    public function import(Request $request){
        //Eliminamos registros anteriores del usuario en sesión:
        TempImportCompanies::where('user_id', Auth::user()->id)->delete();

        //Importamos .xls:
        Excel::import(new CompaniesImport, $request->file('companies'));

        $msg = trans('textos.importacion_ok');
        return redirect()->route($request->business_relationship.'.import')->with(compact('msg'));
    }

    /**
     * 14. Mostrar ficha de empresa.
     */
    public function show(Company $company){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.empresa',
            'module' => $this->module,
            'slug' => 'companies'
        ];

        $origin = 'customers';
        $public_info = [];
        $employees = false;

        $workplaces = Workplace::where('company_id', $company->id)->get();

        return view('admin.companies.show', compact('header', 'company', 'origin', 'public_info', 'employees', 'workplaces'));
    }

    /**
     * 15. Establecer empresa matriz.
     */
    public function setMother(Request $request){
        Company::where('id', $request->company_id)
        ->update(['mother_co' => $request->mother_co]);

        $msg = trans('textos.empresa_matriz_vinculada');
        return redirect()->route('companies.edit', $request->company_id)->with(compact('msg'));
    }

    /**
     * 16. Desvincular de empresa matriz.
     */
    public function unlinkMother(Company $company){
        $company->mother_co = $company->id;
        $company->save();

        $msg = trans('textos.empresa_matriz_desvinculada');
        return redirect()->route('companies.edit', $company)->with(compact('msg'));
    }

    /**
     * 17. Directorio por sectores.
     */
    public function sectors(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.directorio_sectores',
            'module' => $this->module,
            'slug' => 'sectors'
        ];

        $sectors = Category::select('categories.id', 'categories.name', 'categories.slug', 'categories.subcategory')
        ->join('modules', 'categories.module_id', '=', 'modules.id')
        ->where('categories.status', 1)
        ->where('modules.slug', 'companies')
        ->with('sub_category')
        ->orderBy('name', 'ASC')
        ->get();

        return view('admin.company_sectors.index')->with(compact('header', 'sectors'));
    }

    /**
     * 18. Roles por empresa.
     */
    public function getCompanyRoles(Request $request, $company_id){
        if($request->ajax()){
            $data = $this->companyRoles($company_id);

            foreach($data as $r){
                $p = explode('/', $r->name);
                $r->name = $p[1];
            }

            return response()->json($data);
        }
    }

    /**
     * 19. Empresas para autocomplete.
     */
    public function searchCompanies(Request $request){
        $search = $request->search;

        $data = Company::select('id', 'name', 'tradename', 'nif', 'is_user')
        ->where('status', 1)
        ->where('id', '!=', session('currentCompany')) 
        ->where(function($query) use ($search){  
            $query->where('name', 'like', '%'.$search.'%') 
            ->orWhere('tradename', 'like', '%'.$search.'%')   
            ->orWhere('nif', 'like', '%'.$search.'%');
        })
        ->orderBy('name', 'ASC')
        ->limit(15)
        ->get();

        return response()->json($data);
    }

    /**
     * 20. Proveedores para autocomplete.
     */
    public function searchProviders(Request $request){
        $search = $request->search;

        $data = Company::select('companies.id', 'companies.name', 'companies.tradename', 'companies.nif', 'companies.is_user')
        ->join('customer_providers', 'companies.id', '=', 'customer_providers.provider_id')
        ->where('customer_providers.customer_id', session('currentCompany'))
        ->where('companies.status', 1)
        ->where('companies.id', '!=', session('currentCompany')) 
        ->where(function($query) use ($search){  
            $query->where('companies.name', 'like', '%'.$search.'%') 
            ->orWhere('companies.tradename', 'like', '%'.$search.'%')   
            ->orWhere('companies.nif', 'like', '%'.$search.'%');
        })
        ->orderBy('companies.name', 'ASC')
        ->limit(15)
        ->get();

        return response()->json($data);
    }
}
