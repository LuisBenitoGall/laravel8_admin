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
     * 2. Relación de horarios por intervalo de fechas.
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
    public static function getUserTimetable($user_id = false, $from, $to){
        if ($user_id) {
            $data = Timetable::select('timetables.id', 'timetables.hour', 'timetables.date', 'timetables.created_at', 'timetables.flag', 'timetables.revision', 'timetables.comments', 'timetable_items.name', 'timetable_items.type', 'timetable_items.fixed', 'timetable_items.icon', 'timetable_items.min', 'timetable_items.max', 'timetable_items.min_hour')
                ->leftJoin('timetable_items', 'timetables.item_id', '=', 'timetable_items.id')
                ->where('timetables.user_id', $user_id)
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
     * 2. Relación de horarios por intervalo de fechas.
     */
    public static function listUserTimetables($from, $to, $user_id, $workplace_id){
        //Todos los contratos desde la fecha de inicio, obteniendo los contratos cuya fecha de finalización sea posterior a la fecha de inicio del intervalo.
        $employees = Employee::select('start_date', 'end_date')
        ->where('user_id', $user_id)
        ->where('end_date', '>=', $from)
        ->orderBy('start_date', 'ASC')
        ->get();

        //Array de fechas trabajadas en los contratos comprendidas entre el intervalo recibido:
        $arrEmployeeWorkingDays = [];
        foreach($employees as $e){
            $inicio = $e->start_date < $from? $from:$e->start_date;
            $fin = $e->end_date > $to? $to:$e->end_date;
            $arr = Timetable::setRankDatesArr($inicio, $fin);

            //Merge con el array $arrEmployeeWorkingDays:
            $arrEmployeeWorkingDays = array_merge($arrEmployeeWorkingDays, $arr);
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

        //Horarios del usuario:
        $timetables = Timetable::getUserTimetable($user_id, $from, $to);

        //Guardamos en un array todas las fechas trabajadas:
        $arrWorkedDays = [];
        foreach ($timetables as $tt) {
            array_push($arrWorkedDays, $tt->date);
        }
        //Quitamos valores duplicados:
        $arrWorkedDays = array_unique($arrWorkedDays);

        //Calendario del usuario:
        $user_calendar = WorkUserCalendar::getUserCalendar($user_id, $from, $to);
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
            $arrWorkCalendar = [];
            //En otro array las horas de trabajo de la empresa, correspondientes a cada jornada:
            $arrWorkCalendarHours = [];
            if ($work_calendar) {
                foreach ($work_calendar as $wc) {
                    array_push($arrWorkCalendar, $wc->date);

                    //Comprobamos que la fecha no tenga un número de horas propio:
                    $day_hours = array_key_exists($wc->date, $arrUserSelfHours) ? $arrUserSelfHours[$wc->date] : $wc->hours;

                    $arrWorkCalendarHours[$wc->date] = substr($day_hours, 0, -3);
                }
            }
            //Quitamos valores duplicados:
            $arrWorkCalendar = array_unique($arrWorkCalendar);

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
            foreach ($arrFromTo as $date) {
                //Día de la semana:
                $week_day = date('N', strtotime($date));

                $acumuladoDia = 0;
                $arr['valid'] = true;
                $customTimetableDay = false;
                $isWorkedDay = true;

                //Obtenemos el tiempo de trabajo del día según el calendario de Empresa:
                if (in_array($date, $arrWorkCalendar)) {
                    $hoursOfWork = $arrWorkCalendarHours[strval($date)];
                } else {
                    $hoursOfWork = false;
                    $isWorkedDay = false;
                }

                //Comprobamos si el usuario tiene número de horas personalizadas en ese día:
                if ($custom_timetables->count()) {
                    foreach ($custom_timetables as $ct) {
                        //Si la fecha se encuentra en alguno de los intervalos de horarios customizados, se asignan a número de horas a trabajar, el número de horas correspondiente al día de la semana que toque:
                        if ($date >= $ct->from && $date <= $ct->to) {
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

                $fixedTime = false;


                //Si es fecha trabajada y carece de tratamiento especial como fecha personalizada:

                //if(in_array($date, $arrWorkedDays)){
                if(in_array($date, $arrEmployeeWorkingDays)){
                    //Recorremos las fechas trabajadas por el usuario buscando coincidencia con la fecha del array:
                    foreach ($timetables as $row) {
                        if ($row->date == $date) {
                            //Enviamos las fechas ya formateadas:
                            $arr['fecha'] = $row->date ? Carbon::parse($row->date)->format($languages[session()->get('locale')][4]) : '';
                            $arr['date_order'] = $row->date;

                            $arr['id'] = $row->id;
                            $arr['hour'] = $row->hour;
                            $arr['name'] = $row->name;
                            $arr['comments'] = $row->comments;
                            $arr['hoursOfWork'] = '0';
                            $arr['diferencia'] = false;
                            $arr['sign'] = false;
                            $arr['warning'] = false;
                            $arr['icon'] = $row->icon;
                            $arr['add_timetable'] = false;
                            $arr['edit_timetable'] = $edit_timetable;
                            $arr['flag'] = $row->flag;
                            $arr['revision'] = $row->revision;

                            // ******** CAMPOS CALCULADOS: *********
                            //Guardamos el siguiente índice para posteriores consultas:
                            $idx = $k + 1;

                            //Comprobamos si la hora tiene un valor horario mínimo:
                            $hora = ($row->min_hour && $row->min_hour > $row->hour) ? $row->min_hour : $row->hour;

                            //Calculamos el tiempo trabajado sumando los intervalos de tiempo de cada par de Entrada y Salida:
                            if ($row->flag == 1) {
                                //Tomamos la hora de salida obteniendo el siguiente registro y comprobando si existe en el array de días trabajados.
                                if (isset($timetables[$idx]) && in_array($timetables[$idx]->date, $arrWorkedDays)) {
                                    //Si tiene un período fijo de tiempo, y el usuario no tiene horarios personalizados, se lo sumamos a la hora de salida:
                                    if ($row->fixed > 0 && !$customTimetableDay) {
                                        $hasta = helpers::sumaHoras($hora, helpers::getTimeFromMinutes($row->fixed));
                                        $fixedTime = true;

                                    //Si el usuario tiene horarios personalizados, se sustituye el valor de hora fija que tenga la empresa por el valor del horario personalizado, esto es, la prevalencia la marca el horario personalizado sobre el horario fijo:
                                    }elseif($row->fixed > 0 && $customTimetableDay){
                                        $hasta = helpers::sumaHoras($hora, $hoursOfWork);
                                        $fixedTime = true;

                                    } else {
                                        $hasta = $timetables[$idx]->hour;
                                        $fixedTime = false;

                                    }
                                    if ($hasta) $tiempo = helpers::getTimeBetweenHours($hora, $hasta);

                                } else {
                                    $tiempo = helpers::getTimeBetweenHours($hora, date('H:i:s'));
                                    $fixedTime = false;

                                }
                                $totalTime = helpers::sumaHoras($totalTime, $tiempo);
                                $acumuladoDia = helpers::sumaHoras($acumuladoDia, $tiempo);
                                $arr['accumulated'] = '';
                                $setCustomTimetables = false;
                            }

                            //Para jornadas trabajadas sin horario de trabajo preestablecido (ej.: festivos o fines de semana):
                            $hoursOfWork = !$hoursOfWork && $acumuladoDia > 0 ? '0' : $hoursOfWork;

                            //Si el concepto tiene una duración máxima, se suman los minutos de esa duración al tiempo trabajado:
                            if ($row->max > 0) {
                                $totalTime = helpers::sumaHoras($totalTime, helpers::getTimeFromMinutes($row->max));
                                $acumuladoDia = helpers::sumaHoras($acumuladoDia, helpers::getTimeFromMinutes($row->max));
                            }

                            //El acumulado se muestra sólo en el último registro del día:
                            if ($row->flag == 2 && ((isset($timetables[$idx]) && $timetables[$idx]->date != $date) || !isset($timetables[$idx]))){
                                //Si el horario fijo es inferior al personalizado, se toma el horario fijo:
                                // if($fixedTime){
                                //     $real_hours = $acumuladoDia;
                                // }else{
                                //     $real_hours = $hoursOfWork < $acumuladoDia? $hoursOfWork:$acumuladoDia;    
                                // }
                                

                                $real_hours = $fixedTime && $hoursOfWork < $acumuladoDia? $hoursOfWork:$acumuladoDia;

                                $arr['accumulated'] = $real_hours;
                                $arr['diferencia'] = helpers::getTimeBetweenHours($real_hours, $hoursOfWork);
                                $arr['sign'] = $acumuladoDia >= $hoursOfWork ? '+' : '-';
                                $arr['hoursOfWork'] = $hoursOfWork;
                            }
                            array_push($data, $arr);
                            $k++;
                        }
                    }

                //Si NO es fecha trabajada:
                } else {
                    $arr['fecha'] = Carbon::parse($date)->format($languages[session()->get('locale')][4]);
                    $arr['date_order'] = $date;
                    $arr['id'] = '';
                    $arr['hour'] = '';
                    $arr['comments'] = '';
                    $arr['accumulated'] = '';
                    $arr['hoursOfWork'] = '';
                    $arr['diferencia'] = false;
                    $arr['sign'] = false;
                    $arr['warning'] = false;
                    $arr['icon'] = '';
                    $arr['add_timetable'] = $edit_timetable;
                    $arr['edit_timetable'] = false;
                    $arr['flag'] = false;
                    $arr['revision'] = false;

                    //Comprobamos que la fecha no aparezca en el calendario personal del usuario. Si el valor es true debe tener horas trabajadas o las debe si la jornada era de trabajo.
                    $workday = in_array($date, $arrUserCalendar) || in_array($date, $arrEmployeeWorkingDays)? true:false;
                    //$workday = in_array($date, $arrUserCalendar) ? false : true;

                    //Si es jornada laboral, no es jornada libre del usuario y no hay registros del usuario, debe las horas:
                    if ($workday && $isWorkedDay) {
                        //Comprobamos si la fecha existe o no en el calendario laboral:
                        if (in_array($date, $arrWorkCalendar)) {
                            $arr['name'] = trans('textos.registro_falta');
                            $arr['accumulated'] = '0';
                            $arr['hoursOfWork'] = $hoursOfWork;
                            $arr['diferencia'] = $hoursOfWork;
                            $arr['sign'] = '-';
                            $arr['warning'] = true;

                        } else {
                            $arr['valid'] = false;
                        }

                        //Si es jornada laboral de la empresa, no del usuario:
                    } elseif ($workday && !$isWorkedDay) {
                        $arr['name'] = trans('textos.laborable_no');

                    }else{
                        if(isset($arrUserCalendarDayType[strval($date)])){
                            $arr['name'] = ucfirst($arrUserCalendarDayType[strval($date)]);        
                        }else{
                            $arr['name'] = trans('textos.laborable_no');    
                        }
                    }   

                    if ($arr['valid']) {
                        array_push($data, $arr);
                    }
                }
            }
        }

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
