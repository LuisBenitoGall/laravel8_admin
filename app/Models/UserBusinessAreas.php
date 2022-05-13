<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBusinessAreas extends Model{
    /**
     * 1. Areas de negocio por usuario. 
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_business_areas';

    protected $dates = ['deleted_at']; 

    protected $fillable = ['user_id', 'business_area_id'];

    /**
     * 1. Areas de negocio por usuario. 
     */
    public static function getUserBusinessAreas($user_id, $company_id){
        $data = UserBusinessAreas::select('user_business_areas.id', 'user_business_areas.business_area_id', 'business_areas.name')
        ->join('business_areas', 'user_business_areas.business_area_id', '=', 'business_areas.id')
        ->where('user_business_areas.user_id', $user_id)
        ->where('business_areas.company_id', $company_id)
        ->get();
        
        return $data;   
    }  
}
