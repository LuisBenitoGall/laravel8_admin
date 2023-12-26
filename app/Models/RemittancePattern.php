<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RemittancePattern extends Model{
    /**
     * 1. Guardar número customizado.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'remittance_patterns';

    protected $dates = ['deleted_at'];

    /**
     * 1. Guardar número customizado.
     *
     * Genera automáticamente el remittance_num si sólo existe un patrón activo.
     */
    public static function saveRemittanceNum($remittance, $patterns_company, $pattern_id = false){
        if(!$pattern_id){
            $patterns = RemittancePattern::where('company_id', $patterns_company)
            ->where('status', 1)
            ->get();

            if($patterns->count() == 1){
                $pattern = $patterns[0];
            }else{
                $pattern = false;
            }
        }else{
            $pattern = RemittancePattern::find($pattern_id);
        }

        if($pattern){
            $arrPattern = unserialize($pattern->pattern); 

            if($pattern->yearly_reset == 1){
                //Año de la factura:
                $year = $remittance->created_at->year;

                $last_num = Remittance::select('id', 'remittance_num', 'pattern_id', DB::raw('ABS(num_cardinal) AS num_cardinal'))
                ->where('pattern_id', $pattern->id)
                ->whereYear('created_at', $year)
                ->orderBy('num_cardinal', 'DESC')
                ->first();

            }else{
                $last_num = Remittance::select('id', 'remittance_num', 'pattern_id', DB::raw('ABS(num_cardinal) AS num_cardinal'))
                ->where('pattern_id', $pattern->id)
                ->orderBy('num_cardinal', 'DESC')
                ->first();
            }
            $num = $last_num && $last_num->num_cardinal? abs($last_num->num_cardinal)+1:1;

            //Cardinal por defecto, para tomar la siguiente numeración en el momento que se migren los datos actuales:
            // if($num == 1){
            //     $num = config('constants.CARDINAL_DEFAULT_REMITTANCES_') + 1;
            // }

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
                $exist = Remittance::where('remittance_num', $pattern_num)
                ->where('pattern_id', $pattern->id)
                ->first();

                if(!$exist){
                    $is_unique = true;
                }else{
                    $num++;
                    $pattern_num = false;
                }
            }

            $remittance->remittance_num = $pattern_num;
            $remittance->num_cardinal = abs($num);
            $remittance->pattern_id = $pattern->id;
            $remittance->save();

            return ['remittance' => $remittance];

        }else{
            return false;
        }
    }
}
