<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
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
     */
    
    private $option = 'configuracion';
    private $module = 'settings';
    
    public function __construct(){
        $this->middleware('role_get', ['only' => ['edit', 'update']]);
    }   

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        return Validator::make($data, [
            'name' => 'required|string|max:255'
        ]);
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
        $roles = Role::whereNull('company_id')
        ->orWhere('company_id', session('currentCompany'))
        ->get();

        //Permisos del auth:
        $roles_edit = Auth::user()->can('roles.edit');
        $roles_destroy = Auth::user()->can('roles.destroy');

        foreach($roles as $row){
            $row->roles_edit = $roles_edit;
            $row->roles_destroy = $roles_destroy;
        }

        return datatables($roles)->toJson(); 
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
        $role = new Role();
        $role->name = $request->name;
        $role->guard_name = 'web';
        $role->description = $request->description;
        $role->company_id = session('currentCompany');
        $role->save();

        //Guardamos permisos:
        $role->syncPermissions($request->permissions);

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

        $functionalities = Functionality::permissionsJoinModule();
        $permissions = Permission::get();

        //Obtenemos permisos del rol:
        $data = $role->permissions;

        $role_permissions = [];
        foreach($data as $row){
            array_push($role_permissions, $row->id);       
        }   

        $action = 'edit';

        return view('admin.roles.edit', compact('header', 'role', 'functionalities', 'permissions', 'role_permissions', 'action'));    
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

        //1º Actualizar role:
        $role->update($request->all());

        //2º Actualizar permisos:
        $role->syncPermissions($request->permissions);

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
}
