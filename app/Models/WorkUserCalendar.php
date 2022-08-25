<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkUserCalendar extends Model{
    /**
     * 1. Calendario por usuario.
     * 2. Informar si es festivo del empleado.
     * 3. Eliminar registros por fecha y usuario.
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
     * 
     * @param  $id   
     * @param  $date 
     */
    static function noWorkDay($id, $date){
        $data = WorkUserCalendar::select('work_user_calendars.id', 'work_user_calendars.date', 'work_user_calendars.self_hours', 'day_types.is_working', 'day_types.working_day')
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
        WorkUserCalendar::where('user_id', $user_id)
        ->where('date', $date)
        ->delete(); 
    }
}
