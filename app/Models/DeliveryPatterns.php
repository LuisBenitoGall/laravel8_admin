<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryPatterns extends Model{
    /**
     * 1. Generar número de albarán.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'delivery_patterns';

    protected $dates = ['deleted_at'];

    /**
     * 1. Generar número de albarán.
     */
    public static function setDeliveryNum($delivery, $order, $pattern = false){
        if(!$pattern){
            $patterns = DeliveryPatterns::where('company_id', $order->provider_id)
            ->where('status', 1)
            ->get();
        }

        if($pattern || $patterns->count() == 1){
            if($patterns->count() == 1) $pattern = $patterns[0];

            $arrPattern = unserialize($pattern->pattern); 

            if($pattern->yearly_reset == 1){
                //Año del albarán:
                $year = $delivery->created_at->year;

                $last_num = Delivery::select('id', 'delivery_num', 'pattern_id', 
                    DB::raw('ABS(num_cardinal) as num_cardinal'))
                ->where('pattern_id', $pattern->id)
                ->whereYear('created_at', $year)
                ->orderBy('num_cardinal', 'DESC')
                ->first();

            }else{
                $last_num = Delivery::select('id', 'delivery_num', 'pattern_id', 
                    DB::raw('ABS(num_cardinal) as num_cardinal'))
                ->where('pattern_id', $pattern->id)
                ->orderBy('num_cardinal', 'DESC')
                ->first();
            }
            $num = $last_num && $last_num->num_cardinal? abs($last_num->num_cardinal)+1:1;

            $is_unique = false;
            $pattern_num = false;
            while(!$is_unique){
                //Nº de dígitos a completar:
                $arrDigits = str_split($num);
                $numDigits = count($arrDigits);

                //Buscamos el bloque de dígitos en el patrón:
                $bloque_digitos = false;
                foreach($arrPattern as $p){
                    if(strpos($p, '|0') !== false){
                        $bloque_digitos = $p;
                        break;
                    }
                }
                $digitos = trim($bloque_digitos, '|');
                $arrDigitos = str_split($digitos);
                $numDigitos = count($arrDigitos);

                $zeros = $numDigitos - $numDigits;
                $key = '';
                for($i=0; $i<$zeros; $i++){
                    $key .= 0;
                }
                $key .= $num;

                foreach($arrPattern as $sgm){
                    switch($sgm){
                        case '|year|':
                            $pattern_num .= date('Y');
                            break;
                        case $bloque_digitos:
                            $pattern_num .= $key;
                            break;  
                        default:
                            $pattern_num .= $sgm;
                            break;
                    }    
                }

                //Comprobamos que el número creado no exista previamente:
                $exist = Delivery::where('delivery_num', $pattern_num)
                ->where('pattern_id', $pattern->id)
                ->first();

                if(!$exist){
                    $is_unique = true;
                }else{
                    $num++;
                    $pattern_num = false;
                }
            }

            $delivery->delivery_num = $delivery_num;
            $delivery->num_cardinal = abs($num);
            $delivery->pattern_id = $pattern->id;
            $delivery->save();

            return true;

        }else{
            return false;
        }
    }
}
