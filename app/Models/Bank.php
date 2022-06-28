<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bank extends Model{
    /**
     * 1. Importación de datos desde .json.
     * 2. Array de bancos.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'banks';

    protected $fillable = ['name', 'slug', 'tradename', 'lei', 'eu_code', 'supervisor_code', 'status'];

    /**
     * 1. Importación de datos desde .json.
     */
    public function import(){
        $exist = Bank::select('id')->first();

        if(!$exist){   
            $path = storage_path()."/json/banks.json";
            $data = json_decode(file_get_contents($path), true);

            foreach($data as $r){
                $slug = Str::slug($r['name']);

                Bank::create([
                    'name' => $r['name'],
                    'slug' => $slug,
                    'tradename' => $r['tradename'],
                    'lei' => $r['lei'],
                    'eu_code' => $r['eu_code'],
                    'supervisor_code' => $r['supervisor_code'],
                    'status' => $r['status']
                ]);
            }
        } 
    }

    /**
     * 2. Array de bancos.
     */
    public static function getBanks($status = false){
        $data = Bank::select('id', 'name', 'tradename')
        ->where('status', $status)
        ->orderBy('name', 'ASC')
        ->get();

        $banks = [];
        foreach($data as $row){
            $banks[$row->id] = $row->tradename? $row->tradename:$row->name;
        }

        return $banks;
    }
}
