<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Company;
use App\Models\DayTypes;
use App\Models\DayTypesConfig;
use App\Models\Employee;
use App\Models\GroupMembers;
use App\Models\ResiduaryDays;
use App\Models\Timetable;
use App\Models\User;
use App\Models\UserTimetables;
use App\Models\UserWorkplaces;
use App\Models\WorkCalendar;
use App\Models\Workplace;
use App\Models\WorkUserCalendar;

class WorkUserCalendarController extends Controller{
    /**
     * 1. Calendario laboral de usuario.
     */
    
    private $option = 'calendario';
    private $module = 'calendar';
    private $languages;

    public function __construct(){
        $this->middleware('module_setted:calendar');
    }

    /**
     * 1. Calendario laboral de usuario.
     */
    public function index(User $user, $year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.calendario_laboral_empleado'), 
            'module' => $this->module,
            'slug' => 'work-calendar'
        ];

        $today = date('Y-m-d');
        $year = $year? $year:Date('Y');

        return view('admin.work_user_calendar.index')->with(compact('header', 'user', 'today', 'year'));
    }
}
