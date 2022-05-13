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
use App\Models\StaffPick;

class StaffPickController extends Controller{
    /**
     * 1. Procesos de selección.
     * 1.1. Listado de procesos para DataTable. 
     * 2. Crear nuevo proceso.
     * 3. Editar proceso.
     * 4. Actualizar proceso.
     */
    
    private $option = 'seleccion_personal';
    private $module = 'rrhh';
    private $languages;

     public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación update:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required'
            ]);     
        }
    }

    /**
     * 1. Procesos de selección.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.seleccion_procesos',
            'module' => $this->module,
            'slug' => 'staff-pick'
        ]; 

        return view('admin.staff_picks.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de procesos para DataTable.
     */
    public function getProcessesDT(){
        $data = StaffPick::select('id', 'name', 'published_at', 'published_end', 'status')
        ->where('company_id', session('currentCompany'))
        ->orderBy('published_at', 'DESC')
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('staff-pick.edit');
        $eliminar = Auth::user()->can('staff-pick.destroy');

        foreach($data as $row){
            $row->published_from = Carbon::parse($row->published_at)->format($this->languages[session()->get('locale')][4]);
            $row->published_to = Carbon::parse($row->published_end)->format($this->languages[session()->get('locale')][4]);

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($data)->toJson();
    }

    /**
     * 2. Crear nuevo proceso.
     */
    public function create(){
        $process = new StaffPick();
        $process->company_id = session('currentCompany');
        $process->created_by = Auth::user()->id;
        $process->updated_by = Auth::user()->id;
        $process->save();

        $msg = trans('textos.proceso_creado');
        return redirect()->route('staff-pick.edit', $process)->with(compact('msg'));
    }

    /**
     * 3. Editar proceso.
     */
    public function edit(StaffPick $process, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.proceso_editar',
            'module' => $this->module,
            'slug' => 'staff-pick'
        ]; 

        $languages = $this->languages;

        return view('admin.staff_picks.edit')->with(compact('header', 'process', 'tab', 'languages'));
    }

    /**
     * 4. Actualizar proceso.
     */
    public function update(Request $request, StaffPick $process){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/staff-pick/'.$process->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $process->name = $request->name;
        $process->slug = $slug;
        $process->excerpt = $request->excerpt;
        $process->content = $request->content;
        $process->updated_by = Auth::user()->id;
        $process->status = $request->status? 1:0;
        $process->save();

        dd($request->all(), $process);
    }
}
