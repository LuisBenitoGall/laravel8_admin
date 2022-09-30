<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//Models:
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Company extends Model{
    /**
     * 1. Almacenes de la empresa.
     * 2. Tipos de jornada de la empresa.
     * 3. Roles básicos por empresa.
     */

    use HasFactory;
    use SoftDeletes;

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
     * 3. Roles básicos por empresa.
     */
    public function setCompanyRoles(){
        $basic_roles = ['manager', 'basic'];

        //Permisos exclusivos del Super Admin, por lo que no pueden vincularse a ningún otro role:
        $exceptions = config('constants.PERMISSIONS_OFF_');

        $permissions = Permission::all();

        //Pasamos a un array todos los permisos válidos para el manager:
        $arr = [];
        foreach($permissions as $p){

            $segments = explode('.', $p->name);
            if(!in_array($segments[0], $exceptions)){

                array_push($arr, $p->id);
            }

        }

        foreach($basic_roles as $br){

            $role = Company::saveCompanyRole($br, $this->id);

            //Exclusivo para manager:
            if($br == 'manager'){
                $role->givePermissionTo($arr);
               //dd($role->name);
            }
        }
    }

    /**
     * 4. Guardar role por empresa.
     */
    public static function saveCompanyRole($name, $company_id, $description = NULL){
        $role = new Role();
        $role->name = $company_id.'/'.$name;
        $role->guard_name = 'web';
        $role->description = $description;
        $role->company_id = $company_id;
        $role->save();

        return $role;
    }
}
