<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\Schedule;
use App\Models\ScheduleEventTypes;

class ScheduleEventTypesController extends Controller{
    /**
     * 1. Tipos de evento.
     * 1.1. Listado de tipos de evento para DataTable.
     * 2. Formulario nuevo tipo de evento.
     * 3. Guardar nuevo tipo de evento.
     * 4. Editar tipo de evento.
     * 5. Actualizar tipo de evento.
     * 6. Eliminar tipo de evento.
     * 7. Actualizar estado del evento.
     */

    private $option = 'agenda';
    private $module = 'schedule';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:schedule');
        $this->middleware('schedule_event_type_get', ['only' => ['edit', 'update', 'destroy']]);
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
     * 1. Tipos de evento.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.eventos_tipo',
            'module' => $this->module,
            'slug' => 'schedule-event-types'
        ]; 

        return view('admin.schedule_event_types.index')->with(compact('header'));   
    }

    /**
     * 1.1. Listado de tipos de evento para DataTable.
     */
    public function getEventTypesDT(){
        $data= ScheduleEventTypes::where('company_id', session('currentCompany'));

        return DataTables::of($data)
        ->addColumn('event', function($row){
            return $row->name;
        })
        ->addColumn('description', function($row){
            return $row->description;
        })
        ->addColumn('acciones', function($row){
            return view('admin.schedule_event_types.partials.dt-actions', compact('row'));    
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevo tipo de evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.evento_tipo_nuevo',
            'module' => $this->module,
            'slug' => 'schedule-event-types'
        ]; 

        return view('admin.schedule_event_types.create')->with(compact('header'));
    }

    /**
     * 3. Guardar nuevo tipo de evento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/schedule_event_types/create')
                ->withErrors($validator)
                ->withInput();
        }

        $event = new ScheduleEventTypes();
        $event->name = $request->input('name');
        $event->slug = Str::slug($request->name);
        $event->company_id = session('currentCompany');
        $event->user_id = auth()->user()->id;
        $event->description = $request->description;
        $event->status = $request->input('status')? 1:0;
        $event->save();

        $msg = trans('textos.evento_creado');
        return redirect()->route('schedule-event-types.index')->with(compact('msg'));
    }

    /**
     * 4. Editar tipo de evento.
     *
     * @param  \App\event_type  $event_type
     * @return \Illuminate\Http\Response
     */
    public function edit(ScheduleEventTypes $event_type){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.evento_tipo_editar',
            'module' => $this->module,
            'slug' => 'schedule-event-types'
        ]; 

        return view('admin.schedule_event_types.edit')->with(compact('header', 'event_type'));
    }

    /**
     * 5. Actualizar tipo de evento.
     */
    public function update(Request $request, ScheduleEventTypes $event_type){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/schedule-event-types/'.$event_type->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->input('status')? 1:0;

        $event_type->name = $request->input('name');
        $event_type->slug = $slug;
        $event_type->description = $request->description;
        $event_type->status = $status;
        $event_type->save();

        $msg = trans('textos.evento_creado');
        return redirect()->route('schedule-event-types.edit', $event_type->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar tipo de evento.
     *
     * @param  event_type  $event_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScheduleEventTypes $event_type){
        $event_type->delete();

        $msg = trans('textos.evento_deleted');
        return redirect()->route('schedule-event-types.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado del evento.
     */
    public function status(Request $request, ScheduleEventTypes $event_type, $status){
        if($request->ajax()){       
            $event_type->status = $status;
            $event_type->save();

            $resp = ($event_type)? true:false;
            return response()->json($resp);
        }
    }
}
