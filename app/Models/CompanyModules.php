<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyModules extends Model{
    /**
     * 1. Módulos por empresa. 
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'company_modules';

    protected $fillable = ['company_id', 'module_id'];    

    /**
     * 1. Módulos por empresa.
     */
    public static function getCompanyModules($id){
        $data = CompanyModules::select('module_id')->where('company_id', $id)->get();

        return $data;
    } 
}
