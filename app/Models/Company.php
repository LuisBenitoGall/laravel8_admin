<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model{
    /**
     * 1. Almacenes de la empresa. 
     * 2. Tipos de jornada de la empresa.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'companies';

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'slug', 'tradename', 'logo', 'nif', 'ccc', 'is_ute', 'ute_start_at', 'ute_finish_at'];  

    /**
     * 1. Almacenes de la empresa. 
     */
    public function stores(){
        return $this->hasMany(Store::class);
    }  

    /**
     * 2. Tipos de jornada de la empresa.
     */
    public function day_types(){
        return $this->hasMany(DayTypes::class);
    }  
}
