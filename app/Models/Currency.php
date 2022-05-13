<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Currency extends Model{
    /**
     * 1. Importación monedas desde .json.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'currencies';

    protected $fillable = ['name', 'slug', 'code', 'number', 'symbol', 'status'];  

    /**
     * 1. Importación monedas desde .json.
     */
    public static function import(){
        $exist = Currency::select('id')->first();

        if(!$exist){
            $path = storage_path()."/json/currencies.json";
            $currencies = json_decode(file_get_contents($path), true); 

            $symbols = [
                'EUR' => '€',
                'USD' => '$',
                'AUD' => '$',
                'USN' => '$',
                'USS' => '$',
                'CAD' => '$'
            ];

            //Truncate de la tabla:
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('currencies')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach($currencies as $c){
                $number = explode('.', $c['NumericCode']);

                //Sólo guardamos code únicos, evitamos repetidos:
                $exist = Currency::select('id')
                ->where('number', $number[0])
                ->first();

                if(!$exist){
                    $slug = Str::slug($c['Currency']);
                    
                    Currency::create([
                        'name' => $c['Currency'],
                        'slug' => $slug,
                        'code' => $c['AlphabeticCode'],
                        'number' => $number[0],
                        'symbol' => isset($symbols[$c['AlphabeticCode']])? $symbols[$c['AlphabeticCode']]:NULL,
                        'status' => 0
                    ]); 
                }
            }
        }
    }
}
