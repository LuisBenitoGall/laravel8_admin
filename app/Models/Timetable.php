<?php

namespace App\Models;

use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Timetable extends Model{
    /**
     * 1. Horarios de trabajo por usuario.
     * 2. Relación de horarios de usuario por intervalo de fechas.
     * 3. Eliminar registros por fecha y usuario.
     * 4. Array con intervalo de fechas.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'timetables';

    protected $dates = ['deleted_at'];

    /**
     * 1. Horarios de trabajo por usuario.
     */
    public static function getUserTimetable($user_id = false, $from, $to, $authorized = false){
        if ($user_id) {
            $data = Timetable::select('timetables.id', 'timetables.hour', 'timetables.date', 'timetables.created_at', 'timetables.flag', 'timetables.revision', 'timetables.authorized', 'timetables.comments', 'timetable_items.name', 'timetable_items.type', 'timetable_items.fixed', 'timetable_items.fixed_priority', 'timetable_items.icon', 'timetable_items.min', 'timetable_items.max', 'timetable_items.min_hour', 'timetable_items.reviewable')
            ->leftJoin('timetable_items', 'timetables.item_id', '=', 'timetable_items.id')
            ->where('timetables.user_id', $user_id)
            //Condición de item autorizado:
            ->when($authorized, function($q){
                return $q->where('timetables.authorized', true);
            })
            ->whereBetween('timetables.date', [$from, $to])
            ->orderBy('timetables.date', 'ASC')
            ->orderBy('timetables.hour', 'ASC')
            ->get();

            return $data;
        } else {
            return false;
        }
    }

    /**
     * 2. Relación de horarios de usuario por intervalo de fechas.
     */
    public static function listUserTimetables($from, $to, $user_id, $workplace_id, $see_authorized){
        //Todos los contratos desde la fecha de inicio, obteniendo los contratos cuya fecha de finalización sea posterior a la fecha de inicio del intervalo.
        $employees = Employee::select('start_date', 'end_date')
        ->where('user_id', $user_id)
        ->where('end_date', '>=', $from)
        ->orderBy('start_date', 'ASC')
        ->get();

        //Array de fechas trabajadas por el empleado en los contratos comprendidas entre el intervalo recibido:
        $arrEmployeeWorkingDays = [];
        foreach($employees as $e){
            $inicio = $e->start_date < $from? $from:$e->start_date;
            $fin = $e->end_date > $to? $to:$e->end_date;
            //$arr = Timetable::setRankDatesArr($inicio, $fin);
            
            $arrUserDates = Timetable::where('user_id', $user_id)
            ->whereBetween('date', [$inicio, $fin])
            ->pluck('date')
            ->toArray();
            $arrUserDates = array_unique($arrUserDates);

            //Merge con el array $arrEmployeeWorkingDays:
            $arrEmployeeWorkingDays = array_merge($arrEmployeeWorkingDays, $arrUserDates);
        }
        //Quitamos duplicados:
        $arrEmployeeWorkingDays = array_unique($arrEmployeeWorkingDays);

        //Obtenemos fecha de inicio del contrato más antiguo del empleado, pues la fecha $from no puede ser anterior al inicio de la actividad del empleado en la empresa:
        // $employee = Employee::select('start_date')
        // ->where('user_id', $user_id)
        // ->orderBy('start_date', 'ASC')
        // ->first();

        if($employees->count() && strtotime($employees[0]->start_date) > strtotime($from)) $from = $employees[0]->start_date;
        
        //Obtenemos fecha de inicio del contrato más antiguo del empleado, pues la fecha $from no puede ser anterior al inicio de la actividad del empleado en la empresa:
        // $employee = Employee::select('start_date')
        //     ->where('user_id', $user_id)
        //     ->orderBy('start_date', 'ASC')
        //     ->first();

        //if (strtotime($employee->start_date) > strtotime($from)) $from = $employee->start_date;

        //Array del intervalo de fechas:
        $arrFromTo = Timetable::setRankDatesArr($from, $to);

        //Calendario del usuario:
        $user_calendar = WorkUserCalendar::getUserCalendar($user_id, $from, $to);

        //Horarios del usuario por intervalo de fechas. Incluimos si puede acceder a los conceptos bajo autorización:
        $timetables = Timetable::getUserTimetable($user_id, $from, $to, $see_authorized);

        //Guardamos en un array todas las fechas trabajadas:
        $arrUserWorkedDays = [];
        foreach ($timetables as $tt) {
            array_push($arrUserWorkedDays, $tt->date);
        }
        //Quitamos valores duplicados:
        $arrUserWorkedDays = array_unique($arrUserWorkedDays);

        //Guardamos en array las fechas del calendario del usuario:
        $arrUserCalendar = [];
        //Guardamos en array las horas de trabajo propias en días concretos:
        $arrUserSelfHours = [];
        //En otro array el tipo de jornada registrado por el usuario en cada fecha:
        $arrUserCalendarDayType = [];
        foreach ($user_calendar as $uc) {
            array_push($arrUserCalendar, $uc->date);
            $arrUserCalendarDayType[$uc->date] = $uc->name;

            $arrUserSelfHours[$uc->date] = $uc->self_hours;
        }
        //Quitamos valores duplicados:
        $arrUserCalendar = array_unique($arrUserCalendar);

        //Los datos se entregan en un array:
        $data = [];

        if($timetables->count()){
            $languages = helpers::languages();

            //Último contrato del empleado:
            $contract = Employee::select('id')
            ->where('user_id', $user_id)
            ->orderBy('start_date', 'DESC')
            ->first();

            //Calendario laboral del centro de trabajo en el período indicado:
            $work_calendar = false;
            if ($contract && $workplace_id) {
                $work_calendar = WorkCalendar::select('work_calendars.date', 'work_calendars.hours')
                ->where('work_calendars.workplace_id', $workplace_id)
                ->whereNotNull('work_calendars.hours')
                ->whereBetween('work_calendars.date', [$from, $to])
                ->get();
            }

            //Pasamos las fechas del calendario laboral a un array:
            $arrCompanyWorkCalendar = [];
            //En otro array las horas de trabajo de la empresa, correspondientes a cada jornada:
            $arrCompanyWorkCalendarHours = [];
            if ($work_calendar) {
                foreach ($work_calendar as $wc) {
                    array_push($arrCompanyWorkCalendar, $wc->date);

                    //Comprobamos que la fecha no tenga un número de horas propio:
                    $day_hours = array_key_exists($wc->date, $arrUserSelfHours) ? $arrUserSelfHours[$wc->date] : $wc->hours;

                    $arrCompanyWorkCalendarHours[$wc->date] = substr($day_hours, 0, -3);
                }
            }
            //Quitamos valores duplicados:
            $arrCompanyWorkCalendar = array_unique($arrCompanyWorkCalendar);

            //Horarios personalizados:
            $custom_timetables = UserTimetables::select('id', 'from', 'to', 'daily_hours')
            ->where('user_id', $user_id)
            ->whereNull('deleted_at')
            ->whereDate('from', '<=', $to)
            ->whereDate('to', '>=', $from)
            ->get(); 

            //Permisos:
            $edit_timetable = Auth::user()->can('employees-timetable.edit');

            $k = 0;
            $totalTime = 0;
            $totalTiempoATrabajar = 0;
            $noWorkedDays = [];     //Array de fechas no trabajadas:

            //Recorremos el intervalo de fechas determinado:
            foreach($arrFromTo as $date){
                //Día de la semana:
                $week_day = date('N', strtotime($date));

                //05/05/23: omitimos por ahora los fines de semana:
                if($week_day < 6){

                    $acumuladoDia = 0;          //Tiempo de trabajo acumulado por el empleado.
                    $arr['valid'] = true;
                    $arr['fecha'] = $date? Carbon::parse($date)->format($languages[session()->get('locale')][4]) : '';
                    $arr['date_order'] = $date;
                    $customTimetableDay = false;
                    $isWorkedDay = true;

                    //Obtenemos el tiempo de trabajo del día según el calendario de Empresa:
                    if(in_array($date, $arrCompanyWorkCalendar)){
                        $hoursOfWork = $arrCompanyWorkCalendarHours[strval($date)];        //Horas de la jornada laborable.
                    }else{
                        //Si son horas trabajadas fuera del calendario de empresa:
                        if(in_array($date, $arrUserWorkedDays)){
                            $hoursOfWork = '00:00';   
                        }else{
                            $hoursOfWork = false;
                            $isWorkedDay = false;
                        }
                    } 

                    //Comprobamos si el usuario tiene número de horas personalizadas en ese día:
                    if($custom_timetables->count()){
                        foreach ($custom_timetables as $ct) {
                            //Si la fecha se encuentra en alguno de los intervalos de horarios customizados, se asignan a número de horas a trabajar, el número de horas correspondiente al día de la semana que toque:
                            if($date >= $ct->from && $date <= $ct->to){
                                $custom_hours = $ct->daily_hours ? unserialize($ct->daily_hours) : false;

                                //Si teniendo horario personalizado, para el día no hay horas, se considera jornada no trabajada.
                                if (!$custom_hours[$week_day]) {
                                    $isWorkedDay = false;
                                    $hoursOfWork = '0';
                                }

                                if ($custom_hours) {
                                    $hoursOfWork = $custom_hours[$week_day];
                                    $customTimetableDay = true;
                                }

                                //Horario modificado en el calendario:
                                if ($arrUserSelfHours && isset($arrUserSelfHours[$date])) {
                                    $hoursOfWork = $arrUserSelfHours[$date];
                                    $customTimetableDay = true;
                                }
                                break;
                            }
                        }
                    }

                    //Establece si se trata de un registro con duración fija:
                    $fixedTime = false;
                    $priority = false;

                    //Jornada trabajada (se encuentra entre las fechas trabajadas por el empleado):
                    if(in_array($date, $arrUserWorkedDays)){
                        //Recorremos las fechas trabajadas por el usuario buscando coincidencia con la fecha del array:
                        foreach($timetables as $row){ 
                            $arr['id'] = $row->id;

                            //Si la fecha no se encuentra entre las fechas de la empresa y no se halla autorizada, se le pasa valor false:
                            $authorized = !in_array($date,  $arrCompanyWorkCalendar) && !$row->authorized? false:$row->authorized;

                            //Fecha con registros:
                            if($row->date == $date){
                                $arr['hour'] = $row->hour;
                                $arr['name'] = $row->name;
                                $arr['comments'] = $row->comments;
                                $arr['accumulated'] = '';
                                $arr['accumulated_value'] = '';
                                $arr['hoursOfWork'] = $row->flag == 2? $hoursOfWork:false;
                                $arr['diferencia'] = false;
                                $arr['sign'] = false;
                                $arr['warning'] = false;
                                $arr['icon'] = $row->icon;
                                $arr['add_timetable'] = false;
                                $arr['edit_timetable'] = $edit_timetable;
                                $arr['flag'] = $row->flag;
                                $arr['reviewable'] = true;
                                $arr['revision'] = $row->revision;
                                $arr['authorized'] = $authorized;

                                // ******** CAMPOS CALCULADOS: *********
                                //Guardamos el siguiente índice para posteriores consultas:
                                $idx = $k + 1;

                                //Comprobamos si la hora tiene un valor horario mínimo:
                                $hora = ($row->min_hour && $row->min_hour > $row->hour) ? $row->min_hour : $row->hour;

                                //Calculamos el tiempo trabajado sumando los intervalos de tiempo de cada par de Entrada y Salida:
                                if($row->flag == 1){
                                    //Tomamos la hora de salida obteniendo el siguiente registro y comprobando si existe en el array de días trabajados.
                                    if(isset($timetables[$idx]) && in_array($timetables[$idx]->date, $arrUserWorkedDays)){
                                        //Si tiene un período fijo de tiempo, y el usuario no tiene horarios personalizados, se lo sumamos a la hora de salida:
                                        if($row->fixed > 0 && !$customTimetableDay){
                                            $hasta = helpers::sumaHoras($hora, helpers::getTimeFromMinutes($row->fixed));
                                            $fixedTime = true;
                                            $priority = $row->fixed_priority? true:false;

                                        //Si el usuario tiene horarios personalizados, se sustituye el valor de hora fija que tenga la empresa por el valor del horario personalizado, esto es, la prevalencia la marca el horario personalizado sobre el horario fijo:
                                        }elseif($row->fixed > 0 && $customTimetableDay){
                                            $hasta = helpers::sumaHoras($hora, $hoursOfWork);
                                            $fixedTime = true;
                                            $priority = $row->fixed_priority? true:false;

                                        }else{
                                            $hasta = $timetables[$idx]->hour;
                                            $fixedTime = false;
                                        }

                                        //Si está definido el fin del período de tiempo, se calcula:
                                        if($hasta) $tiempo = helpers::getTimeBetweenHours($hora, $hasta);

                                    //Si no existe en el array de días trabajados:
                                    }else{
                                        $tiempo = helpers::getTimeBetweenHours($hora, date('H:i:s'));
                                        $fixedTime = false;
                                    }

                                    $totalTime = helpers::sumaHoras($totalTime, $tiempo);
                                    $acumuladoDia = helpers::sumaHoras($acumuladoDia, $tiempo);
                                    $arr['accumulated'] = '';
                                    $arr['accumulated_value'] = '';
                                    $setCustomTimetables = false;
                                }

                                //Si el concepto tiene una duración máxima, se suman los minutos de esa duración al tiempo trabajado:
                                if($row->max > 0){
                                    $totalTime = helpers::sumaHoras($totalTime, helpers::getTimeFromMinutes($row->max));
                                    $acumuladoDia = helpers::sumaHoras($acumuladoDia, helpers::getTimeFromMinutes($row->max));
                                }

                                //El acumulado se muestra sólo en el último registro del día:
                                if($row->flag == 2){
                                    //Si es el último registro del día:
                                    if( (isset($timetables[$idx]) && $timetables[$idx]->date != $date) || !isset($timetables[$idx])){
                                        //Si el horario fijo es inferior al personalizado, se toma el horario fijo:
                                        $real_hours = $fixedTime && $hoursOfWork < $acumuladoDia? $hoursOfWork:$acumuladoDia;

                                        //Criterio de prioridad:
                                        if($priority){
                                            $real_hours = $acumuladoDia;
                                        }

                                        $arr['accumulated'] = $real_hours;
                                        $arr['accumulated_value'] = $real_hours;
                                        $arr['diferencia'] = helpers::getTimeBetweenHours($real_hours, $hoursOfWork);
                                        $arr['sign'] = $acumuladoDia >= $hoursOfWork ? '+' : '-';
                                        $arr['hoursOfWork'] = $hoursOfWork;
                                    }else{
                                        $arr['hoursOfWork'] = false;    
                                    }
                                }

                                array_push($data, $arr);
                                $k++;
                            }
                        }

                    //Jornada no trabajada (festivo, baja, fin de semana,...):
                    }else{
                        //Si no es laborable de la empresa:
                        if(!in_array($date, $arrCompanyWorkCalendar)){
                            $arr['hour'] = '';
                            $arr['name'] = trans('textos.laborable_no');    //Indicado como no laborable.
                            $arr['comments'] = '';
                            $arr['accumulated'] = '';
                            $arr['accumulated_value'] = '0';
                            $arr['hoursOfWork'] = false;
                            $arr['diferencia'] = false;
                            $arr['sign'] = '+';
                            $arr['warning'] = true;
                            $arr['icon'] = 'umbrella-beach';
                            $arr['add_timetable'] = false;
                            $arr['edit_timetable'] = false;
                            $arr['flag'] = false;
                            $arr['reviewable'] = false;
                            $arr['revision'] = false;
                            $arr['authorized'] = true;
                        }

                        //Comprobamos si es fecha registrada en el calendario del usuario:
                        if(in_array($date, $arrUserCalendar)){
                            $arr['hour'] = '';
                            $arr['name'] = isset($arrUserCalendarDayType[strval($date)])? ucfirst($arrUserCalendarDayType[strval($date)]):''; 
                            $arr['comments'] = '';
                            $arr['accumulated'] = '';
                            $arr['accumulated_value'] = '0';
                            $arr['hoursOfWork'] = false;
                            $arr['diferencia'] = false;
                            $arr['sign'] = '+';
                            $arr['warning'] = true;
                            $arr['icon'] = 'asterisk';
                            $arr['add_timetable'] = false;
                            $arr['edit_timetable'] = false;
                            $arr['flag'] = false;
                            $arr['reviewable'] = $row->reviewable;
                            $arr['revision'] = false;
                            $arr['authorized'] = true;

                        //Comprobamos si es jornada laboral no trabajada por el usuario:
                        }elseif(in_array($date, $arrCompanyWorkCalendar)){
                            $arr['hour'] = '';
                            $arr['name'] = trans('textos.registro_falta'); 
                            $arr['comments'] = '';
                            $arr['accumulated'] = '00:00';
                            $arr['accumulated_value'] = '0';
                            $arr['hoursOfWork'] = $hoursOfWork;
                            $arr['diferencia'] = $hoursOfWork;
                            $arr['sign'] = '-';
                            $arr['warning'] = true;
                            $arr['icon'] = 'question';
                            $arr['add_timetable'] = true;
                            $arr['edit_timetable'] = false;
                            $arr['flag'] = false;
                            $arr['reviewable'] = $row->reviewable;
                            $arr['revision'] = false;
                            $arr['authorized'] = true;    
                        }
                        array_push($data, $arr);
                    }
                }
            }
        }
        return $data;
    }

    public static function listUserTimetables__($from, $to, $user_id, $workplace_id, $see_authorized){
        //Todos los contratos desde la fecha de inicio, obteniendo los contratos cuya fecha de finalización sea posterior a la fecha de inicio del intervalo.
        $employees = Employee::select('start_date', 'end_date')
        ->where('user_id', $user_id)
        ->where('end_date', '>=', $from)
        ->orderBy('start_date', 'ASC')
        ->get();

        //Calendario del usuario:
        $user_calendar = WorkUserCalendar::getUserCalendar($user_id, $from, $to);

        //Array de fechas trabajadas en los contratos comprendidas entre el intervalo recibido:
        $arrEmployeeWorkingDays = [];
        foreach($employees as $e){
            $inicio = $e->start_date < $from? $from:$e->start_date;
            $fin = $e->end_date > $to? $to:$e->end_date;
            $arr = Timetable::setRankDatesArr($inicio, $fin);

            //Merge con el array $arrEmployeeWorkingDays:
            $arrEmployeeWorkingDays = array_merge($arrEmployeeWorkingDays, $arr);
        }

        //Los datos se entregan en un array:
        $data = [];

        return $data;
    }

    /**
     * 3. Eliminar registros por fecha y usuario.
     */
    public static function deleteRowsByUserDate($user_id, $date){
        Timetable::where('user_id', $user_id)
            ->where('date', $date)
            ->delete();
    }

    /**
     * 4. Array con intervalo de fechas.
     */
    public static function setRankDatesArr($from, $to){
        $arr = [$from];
        $from1 = $from;
        while ($from1 < $to) {
            $from1 = strtotime('+1 day', strtotime($from1));
            $from1 = date('Y-m-d', $from1);
            array_push($arr, $from1);
        }
        return $arr;
    }

    public function items(){
        return $this->belongsTo(TimetableItems::class, 'item_id');
    }

}
