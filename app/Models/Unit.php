<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Unit extends Model{
    /**
     * 1. Importación unidades de venta desde .json.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'units';

    protected $fillable = ['name', 'slug', 'symbol', 'translations', 'status'];

    /**
     * 1. Importación unidades de venta desde .json.
     */
    public static function import(){
        $exist = Unit::select('id')->first();

        if(!$exist){
            $path = storage_path()."/json/units.json";
            $units = json_decode(file_get_contents($path), true); 

            //Truncate de la tabla:
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('units')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach($units as $u){
                Unit::create([
                    'name' => $u['name'],
                    'slug' => $u['slug'],
                    'symbol' => $u['symbol'],
                    'translations' => $u['translations'],
                    'status' => $u['status']
                ]); 
            }
        }
    }
}
