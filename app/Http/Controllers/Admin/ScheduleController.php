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
        $this->middleware('schedule_get', ['only' => ['edit', 'update']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);
        }
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

    /**
     * 3. Guardar nueva agenda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/schedules/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->input('status')? 1:0;

        $schedule = new Schedule();
        $schedule->name = $request->input('name');
        $schedule->slug = $slug;
        $schedule->description = $request->description;
        $schedule->company_id = session('currentCompany');
        $schedule->user_id = auth()->user()->id;
        $schedule->status = $status;
        $schedule->color = $request->color;
        $schedule->save();

        $msg = trans('textos.agenda_creada');
        return redirect()->route('schedules.index')->with(compact('msg'));
    }

    /**
     * 4. Editar agenda.
     *
     * @param  \App\schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agenda_editar',
            'module' => $this->module,
            'slug' => 'schedules'
        ]; 

        return view('admin.schedules.edit')->with(compact('header', 'schedule'));   
    }

    /**
     * 5. Actualizar agenda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/schedules/'.$schedule->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->input('status')? 1:0;

        $schedule->name = $request->input('name');
        $schedule->slug = $slug;
        $schedule->description = $request->description;
        $schedule->status = $status;
        $schedule->color = $request->color;
        $schedule->save();

        $msg = trans('textos.agenda_updated');
        return redirect()->route('schedules.edit', $schedule->id)->with(compact('msg'));
    }

    /**
     * 6. Activar - desactivar agenda.
     */
    public function status(Request $request, Schedule $schedule, $status){
        if($request->ajax()){
            $schedule->status = $status;
            $schedule->save();

            $resp = ($schedule)? true:false;
            return response()->json($resp); 
        }   
    }
}
