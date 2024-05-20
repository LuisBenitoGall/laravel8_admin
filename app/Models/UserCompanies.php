<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserCompanies extends Model
{
    /**
     * 1. Empleados por empresa.
     * 2. Sesión de empresas vinculadas al usuario.
     * 3. Usuario en empresa.
     */

    use HasFactory;

    protected $table = 'user_companies';

    protected $fillable = ['user_id', 'company_id', 'categories'];

    /**
     * 1. Empleados por empresa.
     */
    public static function getCompanyUsers($id, $status = false)
    {
        $query = User::select('users.id', 'users.name', 'users.surname', 'user_companies.categories')
            ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->where('user_companies.company_id', $id);
        if ($status) {
            $query->where('users.status', $status);
        }
        $query->orderBy('users.name', 'ASC');
        $data = $query->get();

        return $data;
    }

    /**
     * 2. Sesión de empresas vinculadas al usuario.
     */
    public static function userCompanies()
    {
        $companies = Auth::user()->companies();

        //Limpiamos la session anterior:
        \Session::forget('companies');

        foreach ($companies as $row) {
            if ($row->status) {
                \Session::push('companies', $row);
            }
        }

        return $companies;
    }

    /**
     * 3. Usuario en empresa.
     */
    public static function userInCompany($user_id, $company_id)
    {
        $data = UserCompanies::select('id')
            ->where('user_id', $user_id)
            ->where('company_id', $company_id)
            ->first();

        return $data;
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'user_companies');
    }
}
