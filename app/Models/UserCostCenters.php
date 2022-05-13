<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCostCenters extends Model{
    /**
     * 1. Centros de coste por usuario.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_cost_centers';

    protected $dates = ['deleted_at']; 

    protected $fillable = ['user_id', 'cost_center_id'];

    /**
     * 1. Centros de coste por usuario.
     */
    public static function getUserCostCenters($user_id, $company_id){
        $data = UserCostCenters::select('user_cost_centers.id', 'user_cost_centers.cost_center_id', 'cost_centers.name')
        ->join('cost_centers', 'user_cost_centers.cost_center_id', '=', 'cost_centers.id')
        ->where('user_cost_centers.user_id', $user_id)
        ->where('cost_centers.company_id', $company_id)
        ->get();

        return $data;
    }
}
