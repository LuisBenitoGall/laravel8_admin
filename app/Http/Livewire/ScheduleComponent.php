<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

//Models:
use App\Models\DayTypes;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\ScheduleEvents;
use App\Models\ScheduleEventTypes;
use App\Models\User;

class ScheduleComponent extends Component{
    /**
     * 1. Render componente.
     * 2. Guardar evento.
     * 3. Marcar / desmarcar agenda.
     * 4. Select 2.
     * 5. Obtener eventos.
     * 6. Drag & drop, y modificar horario.
     */

    public $description;
    public $endDate;
    public $endTime;
    public $event_type_id;
    public $event_types;
    public $events;
    public $guests = [];
    public $mywhen;
    public $place;
    public $products;
    public $products_on;
    public $responsible = [];
    public $schedule_id;
    public $schedules;
    public $schedules_on = [];
    public $startDate;
    public $startTime;
    public $title;
    public $users;

    public $lw;

    protected $rules = [
        'schedule_id' => 'required|integer',
        'title' => 'required',
        'startDate' => 'required',
        'startTime' => 'required'
    ];

    public function mount(){
        $this->event_types = ScheduleEventTypes::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $this->products = Product::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        //Agendas:
        $this->schedules = Schedule::where('company_id', session('currentCompany'))
        ->orderBy('by_default', 'DESC')
        ->orderBy('name', 'ASC')
        ->get();

        //Agendas activas:
        foreach($this->schedules as $sd){
            array_push($this->schedules_on, $sd->id);
        }

        $this->users = User::select('users.id', 'users.name', 'users.surname')
        ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
        ->where('user_companies.company_id', session('currentCompany'))
        ->where('users.status', 1)
        ->orderBy('users.name', 'ASC')
        ->get();

        //Seteamos responsable por default:
        array_push($this->responsible, Auth()->user()->id);

        //Indicamos si los formularios se van a procesar mediante Livewire o post:
        $this->lw = false;
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        //Eventos de las agendas activas:
        $this->events = $this->getEvents();

        return view('livewire.schedule-component');
    }

    /**
     * 2. Guardar evento.
     */
    public function store(){
        $this->validate();

        $start_date = session()->get('locale') != 'en'? helpers::convertFecha($this->startDate):$this->startDate;
        $start = $start_date.' '.$this->startTime;

        $end_date = session()->get('locale') != 'en'? helpers::convertFecha($this->endDate):$this->endDate;
        $end = $end_date.' '.$this->endTime;

        $e = new ScheduleEvents();
        $e->title = $this->title;
        $e->slug = Str::slug($this->title);
        $e->description = $this->description;
        $e->schedule_id = $this->schedule_id;
        $e->author = auth()->user()->id;
        $e->start = $start;
        $e->end = $end;
        $e->responsible = $this->responsible? serialize($this->responsible):NULL;
        $e->guests = $this->guests? serialize($this->guests):NULL;
        $e->products = $this->products_on? serialize($this->products_on):NULL;
        $e->place = $this->place;
        $e->save();

        $this->events = $this->getEvents();

        $this->emit('closeAddModal');
    }

    /**
     * 3. Marcar / desmarcar agenda.
     */
    public function selectSchedule(){
        
    }

    /**
     * 4. Select 2.
     */
    public function hydrate(){
        $this->emit('select2_scheduleId');
    }


    /**
     * 5. Obtener eventos.
     */
    public function getEvents(){
        $data = ScheduleEvents::select('schedule_events.*', 'schedules.color')
        ->join('schedules', 'schedule_events.schedule_id', '=', 'schedules.id')
        ->when($this->schedules_on, function($q, $schedules_on){
            return $q->whereIn('schedule_events.schedule_id', $this->schedules_on);
        })
        ->get();
        return json_encode($data);
    }

    /**
     * 6. Drag & drop, y modificar horario.
     */
    public function eventDrop($event, $oldEvent){
        ScheduleEvents::where('id', $event['id'])
        ->update(['start' => $event['start'], 'end' => $event['end']]);
    }

    public function updatedSchedules_on(){
        if(!is_array($this->schedules_on)) return;

        $this->schedules_on = array_filter($this->schedules_on, function($schedule_on){
            return $schedule_on = false;
        });
    }

}
 
    

