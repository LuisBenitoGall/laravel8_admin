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
use App\Models\Company;
use App\Models\DayTypes;
use App\Models\Schedule;
use App\Models\ScheduleEvents;
use App\Models\ScheduleEventTypes;
use App\Models\User;
use App\Models\WorkUserCalendar;

class ScheduleEventsController extends Controller{
    /**
     * 1. Eventos por calendarios.
     * 2. Guardar nuevo evento.
     * 3. Editar evento.
     * 4. Actualizar evento.
     * 5. Eliminar evento.
     * 6. Drop evento.
     * 7. Email a invitados y responsables de evento.
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
     * 1. Eventos por calendarios.
     */
    public function index(Request $request){
        if($request->ajax()){ 
            $events = ScheduleEvents::select('schedule_events.*', 'schedules.color')
            ->join('schedules', 'schedule_events.schedule_id', '=', 'schedules.id')
            ->where('schedules.company_id', session('currentCompany'))
            ->where('schedule_events.status', 1)
            ->where('schedules.status', 1)
            ->get()
            ->toArray();

            // $day_types = DayTypes::select('day_types.id', 'day_types.name', 'day_types.color')
            // ->join('schedules', 'day_types.id', '=', 'schedules.day_type_id')
            // ->where('day_types.company_id', session('currentCompany'))
            // ->where('schedules.status', 1)
            // ->get();

            // foreach($day_types as $dt){
            //     $employees = WorkUserCalendar::select('work_user_calendars.id AS wuc_id', 'work_user_calendars.date', 'users.id', 'users.name', 'users.surname', 'day_types.name AS dayType', 'day_types.color')
            //     ->join('users', 'work_user_calendars.user_id', '=', 'users.id')
            //     ->join('employees', 'users.id', '=', 'employees.user_id')
            //     ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
            //     ->join('day_types', 'work_user_calendars.day_type_id', '=', 'day_types.id')
            //     ->where('contracts.company_id', session('currentCompany'))
            //     //->whereNotNull('work_user_calendars.day_type_id')
            //     ->where('day_types.id', $dt->id)
            //     ->get();

            //     foreach($employees as $row){
            //         $arr = [
            //             'id' => $row->wuc_id,
            //             'title' => $row->name.' '.$row->surname.', '.$row->dayType,
            //             'start' => $row->date,
            //             'end' => $row->date,
            //             'allDay' => true,
            //             'backgroundColor' => $row->color
            //         ];
            //         array_push($events, $arr);
            //     }
            // }

            return response()->json($events);
        }   
    }

    /**
     * 2. Guardar nuevo evento.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-event');
            
            return redirect('admin/schedules')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->title);

        $start = ($request->input('start') && config('app.locale') != 'en')? Helpers::convertFecha($request->input('start'), true):$request->input('start');
        $end = ($request->input('end') && config('app.locale') != 'en')? Helpers::convertFecha($request->input('end'), true):$request->input('end');

        $responsible = $request->responsible? implode(',', $request->responsible):false;
        $guests = $request->guests? implode(',', $request->guests):false;
        $products = $request->products? implode(',', $request->products):false;

        $event = new ScheduleEvents;
        $event->title = $request->input('title');
        $event->slug = $slug;
        $event->description = $request->input('description');
        $event->schedule_id = $request->input('schedule_id');
        $event->author = auth()->user()->id;
        $event->start = $start;
        $event->end = $end;
        $event->responsible = $responsible;
        $event->guests = $guests;
        $event->products = $products;
        $event->place = $request->input('place');
        $event->event_type_id = $request->input('event_type_id');
        $event->save();
    
        $msg = trans('textos.evento_creado');
        return redirect()->route('schedules.index')->with(compact('msg'));
    }
}
