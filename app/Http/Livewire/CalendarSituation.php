<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

//Models:
use App\Models\DayTypes;
use App\Models\WorkCalendar;
use App\Models\WorkUserCalendar;

class CalendarSituation extends Component{
    /**
     * 1. Render componente.
     * 2. Seleccionar fecha.
     * 3. Seleccionar mes.
     */

    public $dayRegisters = [];
    public $dayTypes;
    public $fecha;
    public $languages;
    public $mes_;
    public $mes_num;
    public $monthRegisters = [];
    public $selectedDates = [];
    public $weekDays;
    public $workplace;
    public $workUserCalendar;
    public $year;

    public function mount($year, $workplace){
        $this->dayTypes = DayTypes::where('company_id', session('currentCompany'))->get();
        //Array de colores de los tipos de jornada:
        $arrDayTypesColor = [];
        foreach($this->dayTypes as $dt){
            $arrDayTypesColor[$dt->id] = $dt->color;    
        }
        $this->weekDays = helpers::weekDays();
        $this->workplace = $workplace;
        $this->year = $year;

        //Obtener las fechas ya marcadas:
        $dates_data = WorkCalendar::select('id', 'day_type_id', 'date', 'hours')
        ->where('workplace_id', $workplace->id)
        ->whereYear('date', '=', $year)
        ->orderBy('date', 'ASC')
        ->get();

        //Componemos el array de fecha seleccionadas:
        foreach($dates_data as $dd){
            $this->selectedDates[$dd->date] = [
                'day_type' => $dd->day_type_id, 
                'color' => $dd->day_type_id && isset($arrDayTypesColor[$dd->day_type_id])? $arrDayTypesColor[$dd->day_type_id]:'',
                'hours' => $dd->hours
            ];    
        }

        //Calendario de los usuarios:
        $this->workUserCalendar = WorkUserCalendar::join('user_workplaces', 'work_user_calendars.user_id', '=', 'user_workplaces.user_id')
        ->where('user_workplaces.workplace_id', $workplace->id)
        ->whereYear('work_user_calendars.date', $year)
        ->whereNull('work_user_calendars.self_hours')
        ->distinct()
        ->orderBy('work_user_calendars.date')
        ->pluck('work_user_calendars.date')->toArray();

        //Idiomas:
        $this->languages = helpers::languages();
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.calendar-situation');
    }

    /**
     * 2. Seleccionar fecha.
     */
    public function selectDay($date){
        //Customizando fecha:
        $this->fecha = $date != ''? Carbon::parse($date)->format($this->languages[session()->get('locale')][4]):'';

        $this->dayRegisters = WorkUserCalendar::select('users.name', 'users.surname', 'day_types.name AS dayType', 'day_types.color')
        ->join('users', 'work_user_calendars.user_id', '=', 'users.id')
        ->join('day_types', 'work_user_calendars.day_type_id', '=', 'day_types.id')
        ->join('user_workplaces', 'work_user_calendars.user_id', '=', 'user_workplaces.user_id')
        ->where('user_workplaces.workplace_id', $this->workplace->id)
        ->where('work_user_calendars.date', $date)
        ->whereNull('work_user_calendars.self_hours')
        ->orderBy('dayType', 'ASC')
        ->orderBy('users.name', 'ASC')
        ->get();
    }

    /**
     * 3. Seleccionar mes.
     */
    public function selectMonth($month){
        $this->mes_num = $month;
        //Customizando mes:
        $this->mes_ = $month? ucfirst(helpers::yearMonths($month)).' '.$this->year:'';

        $this->monthRegisters = WorkUserCalendar::select('work_user_calendars.date', 'users.name', 'users.surname', 'day_types.name AS dayType', 'day_types.color')
        ->join('users', 'work_user_calendars.user_id', '=', 'users.id')
        ->join('day_types', 'work_user_calendars.day_type_id', '=', 'day_types.id')
        ->join('user_workplaces', 'work_user_calendars.user_id', '=', 'user_workplaces.user_id')
        ->where('user_workplaces.workplace_id', $this->workplace->id)
        ->whereMonth('work_user_calendars.date', $month)
        ->whereNull('work_user_calendars.self_hours')
        ->orderBy('dayType', 'ASC')
        ->orderBy('users.name', 'ASC')
        ->get();
    }
}
