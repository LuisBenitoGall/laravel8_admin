<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

//Models:
use App\Models\Functionality;
use App\Models\User;
use App\Models\WorkUserCalendar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller{
    /**
     * 1. Listado de permisos.
     * 1.1. Listado de permisos para DataTable.
     * 2. Guardar nuevo permiso.
     * 3. Eliminar permiso.
     * 4. Permisos por usuario.
     * 5. Actualizar permisos de usuario.
     * 6. Permisos por funcionalidad.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';

    public function __construct(){
        $this->middleware('user_get', ['only' => ['userPermissions', 'updateUserPermissions']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => ['required',
                    'string',
                    'max:255',
                    Rule::notIn(['index', 'create', 'show', 'search', 'edit', 'update', 'destroy'])
                ],
                'functionality' => 'required|string'
            ],[
                'name.not_in' => trans('textos.permiso_existente')
                //No funciona correctamente la traducción.
            ]);
        }
    } 

    /**
     * 1. Listado de permisos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.permisos',
            'module' => $this->module,
            'slug' => 'permissions'
        ];  

        $functionalities = Functionality::orderBy('label', 'ASC')->get();

        //Generación permisos sin funcionalidad:
        WorkUserCalendar::setOtherPermissions();

        return view('admin.permissions.index')->with(compact('header', 'functionalities'));
    }

    /**
     * 1.1. Listado de permisos para DataTable.
     */
    public function getPermissionsDT(){
        $permissions = Permission::get();

        //Permisos del auth:
        $permissions_destroy = Auth::user()->can('permissions.destroy');

        foreach($permissions as $row){
            $p = explode('.', $row->name);
            $prefixe = isset($p[1])? ucfirst(trans('textos.'.$p[1])):'';
            $row->permission = $prefixe.' '.strtolower($p[0]);

            $row->permissions_destroy = $permissions_destroy;
        }

        return datatables($permissions)->toJson(); 
    }

    /**
     * 2. Guardar nuevo permiso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-permission');

            return redirect('/admin/permissions')
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->name);
        $permission_ = $request->functionality.'.'.$slug;

        //Guardamos permiso:
        $permission = new Permission();
        $permission->name = $permission_;
        $permission->guard_name = 'web';
        $permission->save();

        $msg = trans('textos.permiso_creado_ok');
        return redirect()->route('permissions.index')->with(compact('msg'));    
    }

    /**
     * 3. Eliminar permiso.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission){
        $permission->delete();

        $msg = trans('textos.permiso_deleted');
        return redirect()->route('permissions.index')->with(compact('msg'));
    }

    /**
     * 4. Permisos por usuario.
     */
    public function userPermissions(User $user){
        $header = ['option' => $this->option, 'suboption' => 'textos.usuario_permisos'];

        $previous_url = url()->previous();

        $roles = $user->roles;
        $user_permissions_obj = $user->getAllPermissions();
        $user_permissions = [];
        foreach($user_permissions_obj as $upo){
            array_push($user_permissions, $upo->id);
        }

        $functionalities = Functionality::permissionsJoinModule();

        $permissions = Permission::get();

        //Obtenemos los permisos vinculados al rol. Estos permisos no pueden ser eliminados si no se desvincula antes al usuario del rol asignado.
        $role_permissions = $user->getPermissionsViaRoles();
        $role_permissions_arr = [];
        foreach($role_permissions as $rp){
            array_push($role_permissions_arr, $rp->id);
        }

        return view('admin.permissions.user')->with(compact('header', 'user', 'previous_url', 'roles', 'user_permissions', 'functionalities', 'permissions', 'role_permissions_arr'));
    }

    /**
     * 5. Actualizar permisos de usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserPermissions(Request $request, User $user){
        //Borramos los permisos anteriores:
        $user_permissions = $user->permissions;
        foreach($user_permissions as $p){
            $user->revokePermissionTo($p->id);    
        }

        $is_admin = false;

        //Permisos propios del role:
        $role_permissions = $user->getPermissionsViaRoles();
        $role_permissions_arr = [];
        foreach($role_permissions as $rp){
            array_push($role_permissions_arr, $rp->id);
        }

        if(count($role_permissions_arr) > 0 || $request->permissions){
            $is_admin = true;
        }

        //Sólo guardamos los permisos que no pertenezcan al role:
        if($request->permissions){
            foreach($request->permissions as $p){
                if(!in_array($p, $role_permissions_arr)){
                    //Guardamos el permiso del usuario:
                    $user->givePermissionTo($p);

                    //Dar permiso al módulo en caso de no estarlo:
                    $permission = Permission::find($p);
                    $parts = $permission? explode('.', $permission->name):false;

                    //Omitimos los permisos sobre módulos:
                    if($parts && strpos($parts[0], 'module_') === false){
                        $functionality = Functionality::select('modules.name')
                        ->join('modules', 'functionalities.module_id', '=', 'modules.id')
                        ->where('functionalities.slug', $parts[0])
                        ->first();

                        $user->givePermissionTo('module_'.$functionality->name);    
                    }

                    $is_admin = true;
                }
            }
        }

        //Si el usuario dispone de algún permiso, adquiere rango de Administrador, de lo contrario se elimina:
        $user->isAdmin = $is_admin;
        $user->save();  

        $msg = trans('textos.permiso_updated');
        return redirect()->route('permissions.user', [$user->id])->with(compact('msg'));
    }

    /**
     * 6. Permisos por funcionalidad.
     */
    public function getFuncionalityPermissions($slug){
        $data = Permission::select('id', 'name')
        ->where('name', 'like', $slug.'.%')
        ->orderBy('id', 'ASC')
        ->get();

        return response()->json($data);
    }
}
