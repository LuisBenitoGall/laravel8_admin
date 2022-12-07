<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DayTypesCriterions extends Model{
    /**
     * 1. Jornadas por semana.
     */
     
    use HasFactory;
    use SoftDeletes;

    protected $table = 'day_types_criterions';

    /**
     * 1. Jornadas por semana.
     *
     * Establece un número máximo de tipos de jornada por semana. 
     * Parámetros:      -nº máximo de días
     *                  -festivos en la semana que anulan esta opción      
     */
    public static function weeklyDayTypes($fecha, $day_type, $workplace_id, $max, $with_weekend = false){
        //Debe ser jornada laborable:
        if($day_type->is_working){
            $weekNum = date("W", strtotime($fecha)); 
            // N   Representación numérica ISO-8601 del día de la semana (añadido en PHP 5.1.0)    1 (para lunes) hasta 7 (para domingo)
            $weekDay = date("N", strtotime($fecha));

            //El número de días laborables de la semana se fija en función de la variable $with_weekend:
            $working_days = $with_weekend? 7:5;

            //Obtenemos el lunes de la semana:
            $restar = $weekDay - 1;
            $weekMonday = date("Y-m-d", strtotime($fecha." - ".$restar." days"));

            $allWeek[] = $weekMonday;
            $i = 1;
            while($i < $working_days){
                $allWeek[] = date("Y-m-d", strtotime($weekMonday." + ".$i." days"));
                $i++;
            }

            //Verificamos si entre los días de la semana existe alguna fecha de tipo festivo (is_working = 1):
            $fecha = WorkCalendar::select('work_calendars.id')
            ->join('day_types', 'work_calendars.day_type_id', '=', 'day_types.id')
            ->where('work_calendars.workplace_id', $workplace_id)
            ->whereIn('work_calendars.date', $allWeek)
            ->where('day_types.is_working', '0')
            ->get();

            $response = [];
            if($fecha->count()){
                $response['is_allowed'] = false;
                $response['msg'] = trans('textos.peticion_no_aceptada');   
            }else{
                $response['is_allowed'] = true;
                $response['msg'] = '';     
            }

            return $response;

        }else{
            return false;
        }        
    }
}
