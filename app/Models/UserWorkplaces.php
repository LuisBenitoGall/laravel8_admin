<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWorkplaces extends Model{
    /**
     * 1. Centros de trabajo por usuario.
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
}
