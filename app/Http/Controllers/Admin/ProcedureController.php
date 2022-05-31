<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\Procedure;
use App\Models\ProcedurePatterns;
use App\Models\ProcedureRevisions;
use App\Models\User;

class ProcedureController extends Controller{
    /**
     * 1. Procedimientos por empresa.
     * 1.1. Listado de procedimientos para DataTable.
     * 2. Formulario nuevo procedimiento.
     * 3. Guardar nuevo procedimiento.
     * 4. Editar procedimiento.
     * 5. Actualizar procedimiento.
     * 6. Eliminar procedimiento.
     */
    
    private $option = 'procedimientos';
    private $module = 'procedures';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:procedures');
        $this->middleware('procedure_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|max:255'
            ]);
        }
    }

    /**
     * 1. Procedimientos por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.procedimientos_mis',
            'module' => $this->module,
            'slug' => 'procedures'
        ]; 

        return view('admin.procedures.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de procedimientos para DataTable.
     */
    public function getProceduresDT(){
        $data = Procedure::select('procedures.*', 'users.name AS nombre', 'users.surname', 
            DB::raw('(SELECT pr.id FROM procedure_revisions pr WHERE pr.procedure_id = procedures.id ORDER BY pr.order DESC LIMIT 1) AS revisionId'),
            DB::raw('(SELECT prr.updated_at FROM procedure_revisions prr WHERE prr.procedure_id = procedures.id ORDER BY prr.order DESC LIMIT 1) AS updated_at')
        )
        ->join('users', 'procedures.updated_by', '=', 'users.id')
        ->where('procedures.company_id', session('currentCompany'))
        ->orderBy('procedures.name', 'ASC')
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('procedures.edit');
        $eliminar = Auth::user()->can('procedures.destroy');

        foreach($data as $row){
            $row->alta = $row->created_at? $row->created_at->format($this->languages[session()->get('locale')][4]):'';

            $row->last_revision = $row->updated_at? $row->updated_at->format($this->languages[session()->get('locale')][4]):'';

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($data)->toJson();
    }

    /**
     * 2. Formulario nuevo procedimiento.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.procedimiento_nuevo',
            'module' => $this->module,
            'slug' => 'procedures'
        ]; 

        return view('admin.procedures.create')->with(compact('header'));    
    }

    /**
     * 3. Guardar nuevo procedimiento.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/procedures/create')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name); 

        $row = new Procedure();
        $row->name = $request->name;
        $row->slug = $slug;
        $row->company_id = session('currentCompany');
        $row->created_by = Auth::user()->id;
        $row->updated_by = Auth::user()->id;
        $row->save();

        $msg = trans('textos.procedimiento_creado');
        return redirect()->route('procedures.edit', $row)->with(compact('msg'));
    }

    /**
     * 4. Editar procedimiento.
     */
    public function edit(Procedure $procedure, $tab = false, $revision = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.procedimiento_editar',
            'module' => $this->module,
            'slug' => 'procedures'
        ]; 

        $revision = $revision? ProcedureRevisions::find($revision):false;

        //Patrones de numeración del procedimiento:
        $patterns = false;
        if(!$procedure->pattern_id){
            $patterns = ProcedurePatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        $author = User::select('name', 'surname')->find($procedure->created_by);

        $revisions = ProcedureRevisions::select('procedure_revisions.*', 'users.name AS nombre', 'users.surname')
        ->join('users', 'procedure_revisions.created_by', '=', 'users.id')
        ->where('procedure_revisions.procedure_id', $procedure->id)
        ->orderBy('procedure_revisions.order', 'ASC')
        ->get();

        $languages = $this->languages;

        return view('admin.procedures.edit')->with(compact('header', 'procedure', 'tab', 'revision', 'patterns', 'author', 'revisions', 'languages'));
    }

    /**
     * 5. Actualizar procedimiento.
     */
    public function update(Request $request, Procedure $procedure){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/procedures/'.$procedure->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        //Número de presupuesto:
        $patternNum = [];
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('procedure', $request->pattern_id);
        }

        //Slug:
        $slug = Str::slug($request->name);

        //Actualizando datos:
        if($request->pattern_id){
            if(!empty($patternNum)){
                $procedure->procedure_num = $patternNum['pattern_num'];  
                $procedure->num_cardinal = $patternNum['num']; 
            }            
            $procedure->pattern_id = $request->pattern_id;
        }
        $procedure->procedure_old_num = $request->procedure_old_num;
        $procedure->name = $request->name;
        $procedure->slug = $slug;
        $procedure->description = $request->description;
        $procedure->updated_by = Auth::user()->id;
        $procedure->status = $request->status? 1:'0';
        $procedure->save();

        $msg = trans('textos.procedimiento_updated');
        return redirect()->route('procedures.edit', $procedure)->with(compact('msg'));
    }

    /**
     * 6. Eliminar procedimiento.
     */
    public function destroy(Procedure $procedure){
        //Eliminamos revisiones:
        ProcedureRevisions::where('procedure_id', $procedure->id)
        ->delete();

        $procedure->delete();

        $msg = trans('textos.procedimiento_deleted');
        return redirect()->route('procedures.index')->with(compact('msg'));
    }
}
