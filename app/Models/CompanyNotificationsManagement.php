<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyNotificationsManagement extends Model{
    /**
     * 1. Usuarios por módulo y empresa.
     */

    use HasFactory;

    protected $table = 'company_notifications_management';

    /**
     * 1. Usuarios por módulo y empresa.
     */
    public static function getCompanyModuleUsers($company_id, $module_id, $array = false){
        if($array){
            $data = CompanyNotificationsManagement::where('company_id', $company_id)
            ->where('module_id', $module_id)
            ->pluck('user_id')
            ->toArray();

        }else{
            $data = CompanyNotificationsManagement::select('user_id')
            ->where('company_id', $company_id)
            ->where('module_id', $module_id)
            ->get();
        }

        return $data;
    }
}
