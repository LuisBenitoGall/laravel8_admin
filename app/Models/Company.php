<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
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
    public function workplaces()
    {
        return $this->hasMany(Workplace::class);
    }

    /**
     * 3. Roles básicos por empresa.
     */
    public function setCompanyRoles($set_permissions = true, $arr = false){
        $basic_roles = ['manager', 'basic'];

        foreach($basic_roles as $br){
            $role = Company::saveCompanyRole($br, $this->id);

            //Exclusivo para manager:
            if($br == 'manager' && $set_permissions){
                if(!$arr){
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
        $role_name = $company_id.'/'.$name;
        $exists = Role::where('name', $role_name)->first();

        if(!$exists){
            $role = new Role();
            $role->name = $role_name;
            $role->guard_name = 'web';
            $role->description = $description;
            $role->company_id = $company_id;
            $role->save();

            return $role;
        }else{
            return false;
        }
    }
}
