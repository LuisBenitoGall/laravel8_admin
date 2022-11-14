<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Employee extends Model{
    /**
     * 1. Empleados por empresa.
     * 2. Empleados por empresa en array.
     */

    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'employees';

    protected $fillable = ['user_id', 'company_id', 'job_title', 'start_date', 'end_date', 'weekly_hours', 'time_control', 'state'. 'observations'];

    /**
     * 1. Empleados por empresa.
     */
    public static function companyEmployees($company_id = false, $status = false){
        if($company_id){
            return Employee::select('users.*')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
            ->where('contracts.company_id', $company_id)
            ->when($status, function($q) use($status){
                return $q->where('employees.status', $status);
            })
            ->groupBy('users.id')
            ->get();

        }else{
            return false;
        }
    }

    /**
     * 2. Empleados por empresa en array.
     */
    public static function companyEmployeesArray($company_id = false, $status = false){
        if($company_id){
            return Employee::select(
            DB::raw("CONCAT(users.name,' ',users.surname) AS user"), 'users.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
            ->where('contracts.company_id', $company_id)
            ->when($status, function($q) use($status){
                return $q->where('employees.status', $status);
            })
            ->groupBy('users.id')
            ->orderBy('users.name')
            ->pluck('user', 'users.id')
            ->toArray();

        }else{
            return false;
        }
    }

    public function users(){
        return $this->belongsTo(User::class);
    }
}
