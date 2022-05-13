<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserCompanies extends Model{
    /**
	 * 1. Empleados por empresa. 
	 * 2. Sesión de empresas vinculadas al usuario.
	 */

    use HasFactory;

    protected $table = 'user_companies';

    protected $fillable = ['user_id', 'company_id'];

    /**
	 * 1. Empleados por empresa. 
	 */
	public static function getCompanyUsers($id, $status = false){
		$query = User::select('users.id', 'users.name', 'users.surname', 'user_companies.categories')
    	->join('user_companies', 'users.id', '=', 'user_companies.user_id')
    	->where('user_companies.company_id', $id);
    	if($status){
    		$query->where('users.status', $status);
    	}
    	$query->orderBy('users.name', 'ASC');
    	$data = $query->get();

    	return $data;
	}

	/**
	 * 2. Sesión de empresas vinculadas al usuario.
	 */
	public static function userCompanies(){
		$companies = Auth::user()->companies();

		//Limpiamos la session anterior:
		\Session::forget('companies');

        foreach($companies as $row){
        	if($row->status){
            	\Session::push('companies', $row);
        	}
        }	

        return $companies;
	}
}
