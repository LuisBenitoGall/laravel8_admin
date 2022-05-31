<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Validator;
use App\Helpers;

class AppServiceProvider extends ServiceProvider{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        //Validate NIF:
        Validator::extend('validate_nif', function($attribute, $value, $parameters){
            return $this->validateNif($value);
        });

        //Validate campos por un tercero:
        Validator::extend('validate_third_part', function($attribute, $value, $parameters){
            return $this->validateFieldsThirdPart($value, $parameters);
        });

        //Validate campo mayor que otro:
        Validator::extend('greater_than_field', function($attribute, $value, $parameters, $validator){
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value > $min_value;
        });   

        Validator::replacer('greater_than_field', function($message, $attribute, $rule, $parameters){
            return str_replace(':field', $parameters[0], $message);
        });
    }

    /**
     * Validación de NIF:
     * 
     * @param  [type] $nif [description]
     * @return [type]      [description]
     */
    public static function validateNif($nif){
        //La empresa debe tener configurada la validación de NIF:
        if($nif != '' && (!session('companySettings') || session('companySettings')->validate_nif)){
            $nif = strtoupper($nif);
            for($i = 0; $i < 9; $i ++){
                $num[$i] = substr($nif, $i, 1);
            }

            /**
             * El formato valida los siguientes documentos:
             * - NIF
             * - EIN EEUU
             */

            //si no tiene un formato válido devuelve error
            if(!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$|^[0-9]{2}-[0-9]{7}$)/', $nif)){
                return false;
            }

            //comprobación de NIFs estandar
            if(preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $nif)){
                if($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($nif, 0, 8) % 23, 1)){
                    return 1;
                }else{
                    return false;
                }
            }

            //comprobación de NIEs
            if(preg_match('/^[XYZ]{1}/', $nif)){
                if($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $nif), 0, 8) % 23, 1)){
                    return 3;
                }else{
                    return false;
                }
            }

            //comprobación de EIN (E.E.U.U.) (formato: NN-NNNNNNN)
            if(preg_match('/^[0-9]{2}-[0-9]{7}/', $nif)){
                return 1;   
            } 

            //Algoritmo para comprobación de codigos tipo CIF
            $cif_codes = 'JABCDEFGHI';
            $sum = (string) self::getCifSum($nif);
            $n = (10 - substr ($sum, -1)) % 10;

            if(preg_match ('/^[ABCDEFGHJNPQRSUVW]{1}/', $nif)) {
                if(in_array ($nif[0], array ('A', 'B', 'E', 'H'))){
                    //Numérico
                    return ($nif[8] == $n);
                }elseif(in_array ($nif[0], array ('K', 'P', 'Q', 'S'))){
                    // Letras
                    return ($nif[8] == $cif_codes[$n]);
                }else{
                    // Alfanumérico
                    if(is_numeric ($nif[8])){
                        return ($nif[8] == $n);
                    }else{
                        return ($nif[8] == $cif_codes[$n]);
                    }
                }
            }

            return false;   
        }else{
            return true;
        }
    }

    /**
     * Cálculo letra de CIF. 
     */
    public static function getCifSum($cif){
        $sum = $cif[2] + $cif[4] + $cif[6];

        for($i = 1; $i<8; $i += 2){
            $tmp = (string) (2 * $cif[$i]);
            $tmp = $tmp[0] + ((strlen ($tmp) == 2) ?  $tmp[1] : 0);
            $sum += $tmp;
        }
        return $sum;
    }

    /**
     * Campos obligatorios en función de un tercero.
     */
    public static function validateFieldsThirdPart($value, $parameters){
        //Leyenda de $parameters:
        // $parameters[0] = valor que se debe cumplir,
        // $parameters[1] = valor recibido,
        // $parameters[2] = valor del tercero.
        
        if($parameters[0] == $parameters[1]){
            //Si no viene declarado ninguno de ambos valores:
            if(!$value && !$parameters[2]){
                return false;
            }else{
                return true;    
            }

        }else{
            return true;
        }        
    }
}
