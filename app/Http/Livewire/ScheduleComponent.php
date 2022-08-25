<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
     */

    public $event_type_id;
    public $event_types;
    public $events;
    public $products;
    public $schedule_id;
    public $schedules;
    public $title;
    public $users;

    public function mount(){
        $this->event_types = ScheduleEventTypes::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $this->products = Product::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $this->schedules = Schedule::where('company_id', session('currentCompany'))
        ->orderBy('by_default', 'DESC')
        ->orderBy('name', 'ASC')
        ->get();

        $this->users = User::select('users.id', 'users.name', 'users.surname')
        ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
        ->where('user_companies.company_id', session('currentCompany'))
        ->where('users.status', 1)
        ->get();
    }

    /**
     * 1. Render componente.
     */
    public function render(){
        $events = ScheduleEvents::get();
        $this->events = json_encode($events);

        return view('livewire.schedule-component');
    }

    /**
     * 2. Guardar evento.
     */
    public function store(){
        dd($this->schedule_id, $this->event_type_id, $this->title);
    }
}
