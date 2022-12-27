<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Company;
use App\Models\Functionality;
use App\Models\Module;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller{
    /**
     * 1. Listado de roles.
     * 1.1. Listado de roles para DataTable.
     * 2. Formulario nuevo rol.
     * 3. Guardar nuevo rol.
     * 4. Editar role.
     * 5. Actualizar role.
     * 6. Eliminar role.
     * 7. Sincronizar permisos sobre los módulos.
     * 8. Actualizar estado administrador de usuarios según su rol.
     * 9. Copiar role a otras empresas.
     * 10. Regenerar archivo de roles.
     * 11. Setear permiso para role.
     * 12. Setear permisos de una funcionalidad.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';
    
    public function __construct(){
        $this->middleware('role_get', ['only' => ['edit', 'update']]);
    }   

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);

        //Validación copiar:
        }elseif(isset($data['submit_copy'])){
            return Validator::make($data, [
                'role_id' => 'required|integer',
                'companies' => 'required'
            ]);
        }
    }

    /**
     * 1. Listado de roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.roles',
            'module' => $this->module,
            'slug' => 'roles'
        ];  

        return view('admin.roles.index')->with(compact('header'));  
    }

    /**
     * 1.1. Listado de roles para DataTable.
     */
    public function getRolesDT(){
        //Roles: según sea Super Admin o no
        if(Auth::user()->hasRole(config('constants.SUPER_ADMIN_'))){
            $data = Role::whereNull('company_id')
            ->orWhere('company_id', session('currentCompany'));
        }else{
            $data = Role::where('name', '!=', config('constants.SUPER_ADMIN_'))
            ->whereNull('company_id')
            ->orWhere('company_id', session('currentCompany'))
            ->orderBy('id', 'ASC');
        }

        return DataTables::of($data)
        ->addColumn('name', function($row){
            if($row->name == config('constants.SUPER_ADMIN_')){
                return $row->name;
            }else{
                $p = explode('/', $row->name);
                return ucfirst($p[1]);
            }
        })
        ->addColumn('description', function($row){
            return $row->description;
        })
        ->addColumn('acciones', function($row){
            return view('admin.roles.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['description'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo rol.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.rol_nuevo',
            'module' => $this->module,
            'slug' => 'roles'
        ]; 

        //Funcionalidades:
        $functionalities = Functionality::permissionsJoinModule();

        $permissions = Permission::get();

        $role = false;

        $action = 'create';

        return view('admin.roles.create')->with(compact('header', 'functionalities', 'permissions', 'role', 'action'));
    }

    /**
     * 3. Guardar nuevo rol.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/roles/create')
                ->withErrors($validator)
                ->withInput();
        }   

        //Guardamos role:
        $role = Company::saveCompanyRole($request->name, session('currentCompany'), $request->description);

        //Guardamos permisos:
        //$role->syncPermissions($request->permissions);

        //Permisos sobre los módulos:
        $this->syncModulePermissions($role);

        $msg = trans('textos.rol_creado_ok');
        return redirect()->route('roles.edit', $role->id)->with(compact('msg'));    
    }

    /**
     * 4. Editar role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.rol_editar',
            'module' => $this->module,
            'slug' => 'roles'
        ]; 

        //Sincronizar permisos sobre los módulos:
        //$this->syncModulePermissions($role);

        $functionalities = Functionality::permissionsJoinModule();
        $permissions = Permission::get();
        $modules = Module::orderBy('label', 'ASC')->get();

        //Obtenemos permisos del rol:
        $data = $role->permissions;

        $module_permissions = [];       //Permisos de módulo.
        $role_permissions = [];         //Permisos de funcionalidad.
        foreach($data as $row){
            //Separamos los permisos según sean de módulo o de funcionalidad:
            if(substr($row->name, 0, 7) === "module_"){
                array_push($module_permissions, $row->id);
            }else{
                array_push($role_permissions, $row->id);   
            }
        }   

        $companies = Company::join('user_companies', 'companies.id', '=', 'user_companies.company_id')
        ->where('user_companies.user_id', session('currentCompany'))
        ->where('companies.id', '!=', session('currentCompany'))
        ->pluck('companies.name', 'companies.id')
        ->toArray();

        $action = 'edit';

        return view('admin.roles.edit', compact('header', 'role', 'functionalities', 'permissions', 'modules', 'module_permissions', 'role_permissions', 'companies', 'action'));    
    }

    /**
     * 5. Actualizar role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/roles/'.$role->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        //dd($request->all());

        //1º Actualizar role:
        //$role->update($request->all());

        //2º Actualizar permisos:
        //$role->syncPermissions($request->permissions);

        //3º Permisos sobre los módulos:
        $this->syncModulePermissions($role);

        //4ª Actualizar situación administrador de usuarios con este rol:
        if($role->permissions->count()){
            $this->syncAdminRoles($role->id);
        }

        $msg = trans('textos.rol_updated');
        return redirect()->route('roles.edit', $role->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role){
        $role->delete();

        $msg = trans('textos.rol_deleted');
        return redirect()->route('roles.index')->with(compact('msg'));
    }

    /**
     * 7. Sincronizar permisos sobre los módulos.
     */
    private function syncModulePermissions($role){
        $role_permissions = $role->permissions;
        if($role_permissions->count()){
            foreach($role_permissions as $rp){
                $p = explode('.', $rp->name);

                //Omitimos los permisos sobre módulos:
                if(strpos($p[0], 'module_') === false){
                    $functionality = Functionality::select('modules.name')
                    ->join('modules', 'functionalities.module_id', '=', 'modules.id')
                    ->where('functionalities.slug', $p[0])
                    ->first();

                    $role->givePermissionTo('module_'.$functionality->name);
                }
            }
        }
    }

    /**
     * 8. Actualizar estado administrador de usuarios según su rol.
     */
    private function syncAdminRoles($role_id){
        User::join('model_has_roles', 'users.id', '=', 'model_id')
        ->where('users.isAdmin', 0)
        ->where('users.status', 1)
        ->where('model_has_roles.role_id', $role_id)
        ->update(['users.isAdmin' => 1]);
    }

    /**
     * 9. Copiar role a otras empresas.
     */
    public function copy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/roles/'.$request->role_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }  

        $main_role = DB::table('roles')->where('id', $request->role_id)->first();

        $permissions = DB::table('role_has_permissions')
        ->where('role_id', $request->role_id)
        ->pluck('permission_id')
        ->toArray();

        foreach($request->companies as $co){
            //Creamos el rol:
            $parts = explode('/', $main_role->name);
            $role = Company::saveCompanyRole($parts[1], $co, $main_role->description); 

            if($role){
                foreach($permissions as $p){
                    $role->givePermissionTo($p);            
                }
            }
        }

        $msg = trans('textos.roles_copiados');
        return redirect()->route('roles.edit', $main_role->id)->with(compact('msg'));
    }

    /**
     * 10. Regenerar archivo de roles.
     *
     * Se regeneran los .json de secondary-menu.json y functionalities.json
     */
    public function sync(){
        $this->jsonFunctionalities();

        $this->jsonOptionalModules();

        $msg = trans('textos.roles_regenerados');
        return redirect()->route('roles.index')->with(compact('msg'));
    }

    /**
     * 11. Setear permiso para role.
     */
    public function setPermission(Request $request, Role $role, $permission, $action, $module_on, $remove_module){
        if($request->ajax()){ 
            //Guardar:
            if($action == 'on'){
                $role->givePermissionTo($permission);   

                //Activar permiso sobre módulo:
                if($module_on != '0' && is_numeric($module_on)){
                    $role->givePermissionTo($module_on);
                    return response()->json(1);     //Activar módulo del permiso.
                }  


            //Eliminar:
            }else{
                $role->revokePermissionTo($permission);

                //Eliminar permiso de módulo:
                if($remove_module != '0' && is_numeric($module_on)){
                    $role->revokePermissionTo($remove_module); 
                    return response()->json(2);     //Desactivar módulo del permiso.   
                }
            }
        }    
    }

    /**
     * 12. Setear permisos de una funcionalidad.
     */
    public function setFunctionalityPermissions(Request $request, Role $role, $functionality, $action, $module_on, $remove_module){
        if($request->ajax()){ 
            $methods = config('constants.METHODS_');

            //Guardar:
            if($action == 'on'){
                foreach($methods as $m){
                    $role->givePermissionTo($functionality.'.'.$m[0]);    
                }  

                //Activar permiso sobre módulo:
                if($module_on != '0' && is_numeric($module_on)){
                    $role->givePermissionTo($module_on);
                    return response()->json(1);     //Activar módulo del permiso.
                }                     

            //Eliminar:
            }else{
                foreach($methods as $m){
                    $role->revokePermissionTo($functionality.'.'.$m[0]);
                }

                //Eliminar permiso de módulo:
                if($remove_module != '0' && is_numeric($module_on)){
                    $role->revokePermissionTo($remove_module);  
                    return response()->json(2);     //Desactivar módulo del permiso.  
                }
            }
        }    
    }
}
