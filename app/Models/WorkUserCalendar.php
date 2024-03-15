<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

//Models:
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WorkUserCalendar extends Model{
    /**
     * 1. Calendario por usuario.
     * 2. Informar si es festivo del empleado.
     * 3. Eliminar registros por fecha y usuario.
     * 4. Inserción permisos sin funcionalidad.
     */
    
    use HasFactory;

    protected $table = 'work_user_calendars';

    /**
     * 1. Calendario por usuario.
     *
     * @param  $user_id  
     * @param  $from
     * @param  $to
     */
    static function getUserCalendar($user_id, $from, $to){
        $data = WorkUserCalendar::select('work_user_calendars.date', 'work_user_calendars.self_hours', 'day_types.name')
        ->leftJoin('day_types', 'work_user_calendars.day_type_id', '=', 'day_types.id')
        ->where('work_user_calendars.user_id', $user_id)
        ->where('work_user_calendars.date', '>=', $from)
        ->where('work_user_calendars.date', '<=', $to)
        ->where('day_types.working_day', false)
        ->get();

        return $data;
    }

    /**
     * 2. Informar si es festivo del empleado.
     * 16/06/2023: incluye si el tipo de jornada debe descontar tiempos por conceptos horarios obligatorios (desayuno, comida,...);
     * 
     * @param  $id   
     * @param  $date 
     */
    static function noWorkDay($id, $date){
        $data = WorkUserCalendar::select('work_user_calendars.id', 'work_user_calendars.date', 'work_user_calendars.self_hours', 'day_types.is_working', 'day_types.working_day', 'day_types.in_office', 'day_types.subtract_required_concept', 'concepts_not_required')
        ->leftJoin('day_types', 'work_user_calendars.day_type_id', '=', 'day_types.id')
        ->where('work_user_calendars.user_id', $id)
        ->whereDate('work_user_calendars.date', $date)
        ->first();

        return $data;
    }  

    /**
     * 3. Eliminar registros por fecha y usuario.
     */
    static function deleteRowsByUserDate($user_id, $date){
        //Comprobamos previamente que el tipo de jornada no sea compatible con jornada laboral:
        $day = WorkUserCalendar::select('work_user_calendars.*', 'day_types.id AS dtId', 'day_types.is_working', 'day_types.working_day')
        ->leftJoin('day_types', 'work_user_calendars.day_type_id', '=', 'day_types.id')
        ->where('work_user_calendars.user_id', $user_id)
        ->where('work_user_calendars.date', $date)
        ->first();

        if($day && $day->dtId && !$day->working_day){
            $day->delete();
        }
    }

    /**
     * 4. Inserción permisos sin funcionalidad.
     */
    public static function setOtherPermissions(){
        //Incluir en este array los permisos a generar.
        $other_permissions = config('constants.OTHER_PERMISSIONS_');
        //Métodos para los permisos:
        $methods = config('constants.METHODS_');

        foreach($other_permissions as $op){
            //Confirmamos que no esté creado previamente:
            $exist = DB::table('permissions')->where('name', 'LIKE', $op.'.%')->first();

            if(!$exist){
                foreach($methods as $method){
                    Permission::create(['name' => strtolower($op).'.'.$method[0]]);
                }    
            }
        }    
    }
}
