<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model{
    /**
     * 1. Empresa del almacén. 
     * 2. Almacenes por empresa por tipo.
     */
    
    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at'];  

    protected $table = 'stores';

    /**
     * 1. Empresa del almacén. 
     */
    public function company(){
        return $this->belongsTo(Company::class);
    }

    /**
     * 2. Almacenes por empresa.
     */
    public static function storesByType($company_id, $type){
        $data = Store::where('company_id', $company_id)
        ->where('type', $type)  
        ->where('status', 1)
        ->orderBy('name', 'ASC')
        ->get();

        return $data;
    }
}
