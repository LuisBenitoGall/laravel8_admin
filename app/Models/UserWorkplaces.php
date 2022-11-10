<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWorkplaces extends Model{
    /**
     * 1. Centros de trabajo por usuario.
     * 2. Centro de trabajo por empleo.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_workplaces';

    protected $dates = ['deleted_at']; 

    protected $fillable = ['user_id', 'workplace_id'];

    /**
     * 1. Centros de trabajo por usuario.
     */
    public static function getUserWorkplaces($user_id, $company_id){ 
        $data = UserWorkplaces::select('user_workplaces.id', 'user_workplaces.workplace_id', 'workplaces.name')
        ->join('workplaces', 'user_workplaces.workplace_id', '=', 'workplaces.id')
        ->where('user_workplaces.user_id', $user_id)
        ->where('workplaces.company_id', $company_id)->get();

        return $data;
    }

    /**
     * 2. Centro de trabajo por empleo.
     */
    public static function getEmployeeWorkplace($user_id, $from, $to){
        $data = Employee::select('workplaces.id', 'workplaces.name')
        ->join('workplaces', 'employees.workplace_id', '=', 'workplaces.id')
        ->where('employees.user_id', $user_id)
        ->where(function($query) use ($from, $to){
            $query->where('employees.start_date', '<=', $from)
            ->orWhere('employees.end_date', '>=', $to);
        })
        ->first();

        // ->where('employees.start_date', '<=', $from)
        // ->where('employees.end_date', '>=', $to)
        

        //var_dump($data);

        return $data;
    }
}
