<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Functionality extends Model{
    /**
     * 1. Funcionalidades join módulos.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'functionalities';

    protected $dates = ['deleted_at']; 

    protected $fillable = ['name', 'slug', 'label', 'module_id'];

    /**
     * 1. Funcionalidades join módulos.
     */
    public static function permissionsJoinModule(){
        $data = Functionality::select('functionalities.*', 'modules.level')
        ->join('modules', 'functionalities.module_id', '=', 'modules.id')
        ->orderBy('functionalities.label', 'ASC')
        ->get();

        return $data;
    }
}
