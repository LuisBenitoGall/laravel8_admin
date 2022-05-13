<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkUserCalendar extends Model{
    /**
     * 1. Calendario por usuario.
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
}
