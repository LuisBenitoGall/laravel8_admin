<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\WorkCalendar;
use App\Models\Workplace;
use App\Models\WorkUserCalendar;

class CompanyCalendarController extends Controller{
    /**
     * 1. Calendario por año y centro de trabajo.
     * 2. Calendario por filtro.
     */

    private $option = 'calendario';
    private $module = 'calendar';
    private $languages;

    public function __construct(){
        $this->middleware('module_setted:calendar');
    }

    /**
     * 1. Calendario por año y centro de trabajo.
     */
    public function index($workplace_id = false, $year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.calendario_empresa'), 
            'module' => $this->module,
            'slug' => 'company-calendar'
        ];

        //Centros de trabajo:
        $workplaces = Workplace::companyWorkplaces(session('currentCompany'));

        $year = $year? $year:date('Y');

        if($workplaces->count() == 1 && !$workplace_id){
            $workplace = Workplace::find($workplaces[0]->id);   
        }elseif($workplace_id){
            $workplace = Workplace::find($workplace_id);
        }else{
            $workplace = false;
        }   

        $company = Company::select('name')->find(session('currentCompany'));

        return view('admin.company_calendar.index')->with(compact('header', 'workplaces', 'year', 'workplace', 'company'));
    }

    /**
     * 2. Calendario por filtro.
     */
    public function calendarBy(Request $request){
        if($request->workplace_id){
            return redirect()->route('company-calendar.index', [$request->workplace_id, $request->year]);  
        }else{
            return redirect()->route('company-calendar.index');
        }       
    }
}
