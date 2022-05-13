<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\Group;
use App\Models\GroupMembers;

class GroupController extends Controller{
    /**
     * 1. Grupos por empresa.
     * 1.1. Listado de grupos para DataTable.
     * 2. Formulario nuevo grupo.
     * 3. Guardar nuevo grupo.
     * 4. Editar grupo.
     * 5. Actualizar grupo.
     * 6. Eliminar grupo.
     * 7. Actualizar estado.
     */
    
    private $option = 'grupos';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
        $this->middleware('group_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        return Validator::make($data, [
            'name' => 'required|max:255',
        ]);
    }

    /**
     * 1. Grupos por empresa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.grupos_mis',
            'module' => $this->module,
            'slug' => 'groups'
        ]; 

        return view('admin.groups.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de grupos para DataTable.
     */
    public function getGroupsDT(){
        $groups = Group::where('company_id', session('currentCompany'))->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('groups.edit');
        $eliminar = Auth::user()->can('groups.destroy');

        foreach($groups as $row){
            //Enviamos las fechas ya formateadas:
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;
        }

        return datatables($groups)->toJson();
    }

    /**
     * 2. Formulario nuevo grupo.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.grupo_nuevo',
            'module' => $this->module,
            'slug' => 'groups'
        ]; 

        return view('admin.groups.create')->with(compact('header')); 
    }

    /**
     * 3. Guardar nuevo grupo.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/groups/create')
                ->withErrors($validator)
                ->withInput();
        }  

        $slug = Str::slug($request->name);
        $status = $request->input('status')? 1:0;

        $group = new Group();
        $group->company_id = session('currentCompany');
        $group->name = $request->input('name');
        $group->slug = $slug;
        $group->status = $status;
        $group->save();

        $msg = trans('textos.grupo_creado_ok');
        return redirect()->route('groups.edit', $group->id)->with(compact('msg'));
    }

    /**
     * 4. Editar grupo.
     */
    public function edit(Group $group, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.grupo_editar',
            'module' => $this->module,
            'slug' => 'groups'
        ]; 

        //$employees = Employee::companyEmployees(session('currentCompany'));
        $employees = false;

        return view('admin.groups.edit', compact('header', 'group', 'tab', 'employees'));   
    }

    /**
     * 5. Actualizar grupo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/groups/'.$group->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->name);
        $status = $request->input('status')? 1:0;

        $group->name = $request->input('name');
        $group->slug = $slug;
        $group->description = $request->input('description');
        $group->status = $status;
        $group->save();

        $msg = trans('textos.grupo_updated');
        return redirect()->route('groups.edit', $group->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar grupo.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group){
        //Eliminamos los miembros del grupo:
        GroupMembers::where('group_id', $group->id)->delete();

        $group->delete();  

        $msg = trans('textos.grupo_deleted');
        return redirect()->route('groups.index')->with(compact('msg'));   
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $group = Group::find($id);
            $group->status = $status;
            $group->save();

            $resp = ($group)? true:false;
            return response()->json($resp);
        }
    }
}
