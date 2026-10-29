<?php

namespace App\Models;

use App\Helpers\helpers;
use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

//Models:
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Company extends Model{
    /**
     * 1. Almacenes de la empresa.
     * 2. Tipos de jornada de la empresa.
     * 3. Roles básicos por empresa.
     * 4. Guardar role por empresa.
     * 5. Guardar nueva empresa.
     */

    use HasFactory;
    use SoftDeletes;
    use Searchable;

    protected $table = 'companies';

    protected $dates = ['deleted_at'];

    protected $fillable = ['mother_co', 'name', 'slug', 'tradename', 'logo', 'nif', 'ccc', 'is_ute', 'ute_start_at', 'ute_finish_at'];

    /**
     * 1. Almacenes de la empresa.
     */
    public function stores(){
        return $this->hasMany(Store::class);
    }

    /**
     * 2. Tipos de jornada de la empresa.
     */
    public function day_types(){
        return $this->hasMany(DayTypes::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workplaces(){
        return $this->hasMany(Workplace::class);
    }

    /**
     * 3. Roles básicos por empresa.
     */
    public function setCompanyRoles($set_permissions = true, $arr = false){
        $basic_roles = ['basic'];       //18/05/24: temporalmente sólo se genera un rol, por el enorme consumo de recursos para generar todos los permisos necesarios         'manager', 

        foreach ($basic_roles as $br) {
            $role = Company::saveCompanyRole($br, $this->id);

            //Exclusivo para manager:
            if ($br == 'manager' && $set_permissions) {
                if (!$arr) {
                    //Permisos exclusivos del Super Admin, por lo que no pueden vincularse a ningún otro role:
                    $exceptions = config('constants.PERMISSIONS_OFF_');

                    $permissions = Permission::all();

                    dd($permissions);

                    //Pasamos a un array todos los permisos válidos para el manager:
                    $arr = [];
                    foreach ($permissions as $p) {
                        $segments = explode('.', $p->name);
                        if (!in_array($segments[0], $exceptions)) {
                            array_push($arr, $p->id);
                        }
                    }
                }

                $role->givePermissionTo($arr);
            }
        }
    }

    /**
     * 4. Guardar role por empresa.
     */
    public static function saveCompanyRole($name, $company_id, $description = NULL){
        //Comprobamos que no exista el role:
        $role_name = $company_id . '/' . $name;
        $exists = Role::where('name', $role_name)->first();

        if (!$exists) {
            $role = new Role();
            $role->name = $role_name;
            $role->guard_name = 'web';
            $role->description = $description;
            $role->company_id = $company_id;
            $role->save();

            return $role;
        } else {
            return false;
        }
    }

    /**
     * 5. Guardar nueva empresa.
     */
    public static function saveCompany($request){
        //Slug:
        $slug = Str::slug($request->name);

        //Subir logo:
        if($request->hasFile('logo')){
            $file = $request->file('logo');
            $path = public_path().'/img/companies';
            $fileName = uniqid().$file->getClientOriginalName();
            $moved = $file->move($path, $fileName);

        }else{
            $fileName = NULL;
        }
        //$fileName = $this->loadLogo($request, 'companies');

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

        //Vinculamos como cliente o proveedor de la empresa con sesión:
        }else{
            //Creando usuario:
            if($request->email){
                //Comprobamos email:
                $user = User::select('id')
                ->where('email', $request->email)
                ->first();

                if(!$user){
                    $user = new User();
                    $user->name = $request->name;
                    $user->surname = $request->surname;
                    $user->email = $request->email;
                    $user->save();
                }

                //¿GUARDAR ROL?

                //Vinculamos usuario con empresa:
                UserCompanies::firstOrCreate(
                    ['user_id' => $user->id, 'company_id' => $company->id,],
                    ['user_id' => $user->id, 'company_id' => $company->id, 'categories' => $request->job_title]
                );
            }

            if($request->business_relationship == 'customers'){
                $customer_id = $company->id;
                $provider_id = session('currentCompany');
                $account_to = 'customer';
                $mode = 1;  //Helper: accountingAccountModes();
            }else{
                $customer_id = session('currentCompany');
                $provider_id = $company->id;
                $account_to = 'provider';
                $mode = 2;  //Helper: accountingAccountModes();
            }

            $cp = new CustomerProviders();
            $cp->customer_id = $customer_id;
            $cp->provider_id = $provider_id;
            $cp->save();

            //Creando cuenta contable:
            $accounting_account_data = [
                'account_to' => $account_to,
                'company_target' => $company->id,
                'mode' => $mode,
                'with_iva' => 1
            ];
            AccountingAccount::setAccountingAccount($accounting_account_data);
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


    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function orderSettings()
    {
        return $this->hasMany(OrderSettings::class);
    }

    public function budgetSettings()
    {
        return $this->hasMany(BudgetSettings::class);
    }

    public function budgetPatterns()
    {
        return $this->hasMany(BudgetPatterns::class);
    }

    public function businessAreas()
    {
        return $this->hasMany(BusinessAreas::class);
    }

    public function orderPatterns()
    {
        return $this->hasMany(OrderPatterns::class);
    }

    public function purchasePatterns()
    {
        return $this->hasMany(PurchasePatterns::class);
    }

    public function provider()
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    public function finalCustomer()
    {
        return $this->hasMany(Order::class, 'final_customer_id');
    }

    public function timeTableItems()
    {
        return $this->hasMany(TimetableItems::class, 'company_id');
    }

    public function module()
    {
        return $this->belongsToMany(Module::class, 'company_modules');
    }

    public function settings()
    {
        return $this->hasOne(CompanySettings::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'user_companies');
    }
    public function staffPicks()
    {
        return $this->hasMany(StaffPick::class)->where('status',true);
    }
}
