<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /**
     * 1. Empresas del usuario.
     * 2. Imágenes del usuario.
     * 3. Documentos del usuario.
     * 4. Permisos del rol.
     * 5. Usuarios por empresa.
     * 6. Preferencias de usuario.
     * 7. Teléfonos del usuario.
     * 8. Usuario como Super Admin.
     * 9. Notas del usuario.
     * 10. Validación usuario para middleware.
     * 11. Listado de categorias del usuario.
     */

    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use Searchable;

    /**
     * Columna de borrado lógico.
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'nickname', 'remember_token', 'isAdmin', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
        'isAdmin' => 'boolean',
    ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * 1. Empresas del usuario.
     */
    public function companies()
    {
        $data = Company::select('companies.*')
            ->join('user_companies', 'companies.id', '=', 'user_companies.company_id')
            ->where('user_companies.user_id', $this->id)
            ->where('companies.status', 1)
            ->get();

        return $data;
    }

    /**
     * 2. Imágenes del usuario.
     */
    public function images()
    {
        return $this->hasMany(UserImage::class);
    }

    /**
     * 3. Documentos del usuario.
     */
    public function docs()
    {
        return $this->hasMany(UserDoc::class);
    }

    /**
     * 3. Permisos del usuario.
     */
    // static function userPermissions($user_id = false){
    //     if($user_id){
    //         $permisos = DB::table('permission_user')
    //         ->select('permissions.name', 'permissions.slug', 'permission_user.permission_id', 'permission_user.created_at', 'functionalities.name AS Functionality')
    //         ->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')
    //         ->join('functionalities', 'permissions.functionality', '=', 'functionalities.slug')
    //         ->where('permission_user.user_id', $user_id)
    //         ->get();

    //         return $permisos;

    //     }else{
    //         return false;
    //     }
    // }

    /**
     * 4. Permisos del rol.
     */
    // static function rolePermissions($role_id = false){
    //     if($role_id){
    //         $permisos = DB::table('permission_role')
    //         ->select('permission_id', 'role_id')
    //         ->where('role_id', $role_id)
    //         ->get();

    //         return $permisos;

    //     }else{
    //         return false;
    //     }
    // }

    /**
     * 5. Usuarios por empresa.
     */
    public static function companyUsers($company_id, $status = false, $limit = false)
    {
        $q = User::select('users.*', 'user_companies.categories',
            DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image')
        )
            ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->where('user_companies.company_id', session('currentCompany'));
        if ($status) {
            $q->where('users.status', $status);
        }
        if ($limit) {
            $q->limit($limit);
        }
        $users = $q->groupBy('users.id')->get();

        return $users;
    }

    /**
     * 6. Preferencias de usuario.
     */
    public function preferences()
    {
        return $this->hasMany(Preference::class);
    }

    /**
     * 7. Teléfonos del usuario.
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * 8. Usuario como Super Admin.
     */
    public function isSuperAdmin()
    {
        $roles = $this->getRoleNames();

        $sa = false;
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role == config('constants.SUPER_ADMIN_')) {
                    $sa = true;
                    break;
                }
            }
        }

        return $sa;
    }

    /**
     * 9. Notas del usuario.
     */
    public function notes()
    {
        return $this->hasMany(Note::class)->count();
    }

    public static function getProfilePhotoUrlAttribute()
    {
        return false;
    }

    /**
     * 10. Validación usuario para middleware.
     */
    public static function userMiddleware($user_id)
    {
        //Si el usuario es distinto de la sesión:
        if (Auth::user()->id != $user_id) {
            $alert = trans('textos.no_permisos_edicion');

            $user = User::find($user_id);

            //El usuario debe pertenecer a la empresa en sesión:
            $companies = $user->companies();
            $exist = false;
            foreach ($companies as $c) {
                if ($c->id == session('currentCompany')) {
                    $exist = true;
                    break;
                }
            }
            if (!$exist) {
                return redirect()->route('users.index')->with(compact('alert'));
            }

            //Si es Super Admin:
            if (Auth::user()->hasRole(config('constants.SUPER_ADMIN_'))) {
                //Para editar a otro Super Admin, comprobamos que tenga permisos de Omni User:
                if ($user->hasRole(config('constants.SUPER_ADMIN_')) && !in_array(Auth::user()->id, config('constants.OMNI_USERS_'))) {
                    return redirect()->route('users.index')->with(compact('alert'));
                }

                //No es Super Admin:
            } else {
                //Un rol NO Super Admin no puede editar a un Super Admin
                if ($user->hasRole(config('constants.SUPER_ADMIN_'))) {
                    return redirect()->route('users.index')->with(compact('alert'));
                }

                //Comprobamos que tenga permiso de edición de usuarios:
                if (!Auth::user()->hasPermissionTo('users.edit')) {
                    return redirect()->route('users.index')->with(compact('alert'));
                }
            }
        }
    }

    /**
     * 11. Listado de categorias del usuario.
     */
    public function categories_of_user_string($categories_raw)
    {
        // Si el campo es null, se retorna un string vació
        if ($categories_raw == null) {
            return "";
        }

        // Se obtiene las categorias y se concatena el nombre
        $cats_id = explode(',', $categories_raw);
        $categories = Category::whereIn("id", $cats_id)->get();
        $categories_string = "";
        foreach ($categories as $category) {
            $categories_string .= "{$category->name}, ";
        }
        return $categories_string;
    }

    public function workplaces()
    {
        return $this->hasMany(Workplace::class);
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

    public function invoicePatterns()
    {
        return $this->hasMany(InvoicePatterns::class);
    }

    public function invoiceSettings()
    {
        return $this->hasMany(InvoiceSettings::class);
    }

    public function devicePush()
    {
        return $this->hasOne(DevicePush::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }



}
