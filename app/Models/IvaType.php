<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IvaType extends Model{
    /**
     * 1. Importación de datos desde .json.
     */
     
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iva_types';

    protected $fillable = ['name', 'iva', 'equivalence_surcharge', 'status'];

    protected $dates = ['deleted_at'];

    /**
     * 1. Importación de datos desde .json.
     */
    public function import(){
        $exist = IvaType::select('id')->first();

        if(!$exist){   
            $path = storage_path()."/json/iva-types.json";
            $data = json_decode(file_get_contents($path), true);

            foreach($data as $r){
                IvaType::create([
                    'name' => $r['name'],
                    'iva' => $r['iva'],
                    'equivalence_surcharge' => $r['equivalence_surcharge'],
                    'status' => $r['status']
                ]);
            }
        } 


    }
}
