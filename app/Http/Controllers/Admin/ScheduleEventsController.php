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

    //0. Validator:
    protected function validator(array $data){
        //Validación save event:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'title' => 'required|string|max:191',
                'schedule_id' => 'required|integer',
                'startDate' => 'required',
                'startTime' => 'required',
                'endDate' => 'required',
                'endTime' => 'required'
            ]);

        //Validación update event:
        }elseif(isset($data['submit_update'])){
             return Validator::make($data, [
                'id' => 'required|integer',
                'title' => 'required|string|max:191',
                'schedule_id' => 'required|integer',
                'startDate' => 'required',
                'startTime' => 'required',
                'endDate' => 'required',
                'endTime' => 'required'
            ]);
        }
    }

    /**
     * 1. Eventos por calendarios.
     */
    public function index(Request $request){
        if($request->ajax()){ 
            $events = ScheduleEvents::select('schedule_events.*', 'schedules.color AS eventColor')
            ->join('schedules', 'schedule_events.schedule_id', '=', 'schedules.id')
            ->where('schedules.company_id', session('currentCompany'))
            ->where('schedule_events.status', 1)
            ->where('schedules.status', 1)
            ->get()
            ->toArray();

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

        $start_date = config('app.locale') != 'en'? helpers::convertFecha($request->input('startDate')):$request->input('startDate');
        $start = $start_date.' '.$request->startTime;
        
        $end_date = config('app.locale') != 'en'? helpers::convertFecha($request->input('endDate')):$request->input('endDate');
        $end = $end_date.' '.$request->endTime;

        $responsible = $request->responsible? serialize($request->responsible):NULL;
        $guests = $request->guests? serialize($request->guests):NULL;
        $products = $request->products_on? serialize($request->products_on):NULL;

        $event = new ScheduleEvents;
        $event->title = $request->input('title');
        $event->slug = Str::slug($request->title);
        $event->description = $request->input('description');
        $event->schedule_id = $request->input('schedule_id');
        $event->author = auth()->user()->id;
        $event->start = $start;
        $event->end = $end;
        $event->responsible = $responsible;
        $event->guests = $guests;
        $event->products = $products;
        $event->place = $request->input('place');
        //$event->event_type_id = $request->input('event_type_id');
        $event->save();
    
        $msg = trans('textos.evento_creado');
        return redirect()->route('schedules.index')->with(compact('msg'));
    }

    /**
     * 3. Editar evento.
     */
    public function edit(Request $request, ScheduleEvents $event){
        if($request->ajax()){
            //Envíamos las fechas de start y end ya formateadas:
            $inicio = Carbon::parse($event->start)->format($this->languages[session()->get('locale')][4].' H:i');
            $event->inicio = $inicio;

            $fin = Carbon::parse($event->end)->format($this->languages[session()->get('locale')][4].' H:i');
            $event->fin = $fin;

            //Autor:
            $autor = User::select('name', 'surname')->where('id', $event->author)->first();
            $event->autor = $autor->name.' '.$autor->surname;

            //Responsables:
            $event->responsible = $event->responsible? unserialize($event->responsible):false;

            //Invitados:
            $event->guests = $event->guests? unserialize($event->guests):false;

            //Productos:
            $event->products = $event->products? unserialize($event->products):false;

            return response()->json($event);
        }
    }

    /**
     * 4. Actualizar evento.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-event');
            
            return redirect('admin/schedules')
                ->withErrors($validator)
                ->withInput();
        }

        $start_date = config('app.locale') != 'en'? helpers::convertFecha($request->input('startDate')):$request->input('startDate');
        $start = $start_date.' '.$request->startTime;
        
        $end_date = config('app.locale') != 'en'? helpers::convertFecha($request->input('endDate')):$request->input('endDate');
        $end = $end_date.' '.$request->endTime;
      
        $responsible = $request->responsible? serialize($request->responsible):NULL;
        $guests = $request->guests? serialize($request->guests):NULL;
        $products = $request->products_on? serialize($request->products_on):NULL;

        $event = ScheduleEvents::find($request->id);
        $event->title = $request->input('title');
        $event->slug = Str::slug($request->title);
        $event->description = $request->input('description');
        $event->schedule_id = $request->input('schedule_id');
        $event->start = $start;
        $event->end = $end;
        $event->responsible = $responsible;
        $event->guests = $guests;
        $event->products = $products;
        $event->place = $request->input('place');
        //$event->event_type_id = $request->input('event_type_id');
        $event->save();
    
        $msg = trans('textos.evento_updated');
        return redirect()->route('schedules.index')->with(compact('msg'));    
    }
}
