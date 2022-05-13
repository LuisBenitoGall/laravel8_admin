<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model{
    /**
     * 1. Funcionalidades del módulo.
     * 2. Módulo por slug.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'modules';

    protected $dates = ['deleted_at']; 

    protected $fillable = ['name', 'slug', 'label', 'color','icon', 'level', 'translations', 'explanation', 'status'];    

    /**
     * 1. Funcionalidades del módulo.
     */
    public function functionalities(){
        return $this->hasMany(Functionality::class);
    }

    /**
     * 2. Módulo por slug.
     */
    public static function moduleBySlug($slug){
        return Module::where('slug', $slug)->first();
    }
}
