<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\DayTypes;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\ScheduleEventTypes;
use App\Models\User;

class ScheduleController extends Controller{
    /**
     * 1. Agenda.
     * 2. Formulario nueva agenda.
     * 3. Guardar nueva agenda.
     * 4. Editar agenda.
     * 5. Actualizar agenda.
     * 6. Activar - desactivar agenda.
     */

    private $option = 'agenda';
    private $module = 'schedule';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:schedule');
    }

    /**
     * 1. Agenda.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agendas',
            'module' => $this->module,
            'slug' => 'schedules'
        ]; 

        return view('admin.schedules.index')->with(compact('header'));
    }

    /**
     * 2. Formulario nueva agenda.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agenda_nueva',
            'module' => $this->module,
            'slug' => 'schedules'
        ]; 

        return view('admin.schedules.create')->with(compact('header'));
    }

}
