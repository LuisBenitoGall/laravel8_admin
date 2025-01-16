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
use File;

//Models:
use App\Models\BriefingTemplate;
use App\Models\BriefingTemplateItem;

class BriefingTemplateController extends Controller{
    /**
     * 1. Listado de templates por empresa.
     * 1.1. Listado de templates para DataTable.
     * 2. Guardar nuevo template.
     * 3. Editar template.
     * 4. Actualizar template.
     * 5. Eliminar template.
     */
    
    private $option = 'presupuestos';
    private $module = 'budgets';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:budgets');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:191'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:191'
            ]); 
        }
    }

    /**
     * 1. Listado de templates por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.briefing_plantillas',
            'module' => $this->module,
            'slug' => 'briefing-templates'
        ];  

        return view('admin.briefing_templates.index')->with(compact('header'));  
    }

    /**
     * 1.1. Listado de templates para DataTable.
     */
    public function getTemplatesDT(){
        $data = BriefingTemplate::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC');

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('conditions', function($row){
            return $row->conditions;
        })
        ->addColumn('date', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('acciones', function($row){
            return view('admin.briefing_templates.partials.dt-actions', compact('row'));
        })
        ->rawColumns(['conditions'])
        ->make(true);
    }

    /**
     * 2. Guardar nuevo template.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-template');
            
            return redirect('/admin/briefing-templates')
                ->withErrors($validator)
                ->withInput();
        }

        $name = Str::lower($request->name);
        $slug = Str::slug($request->name);

        $tmp = new BriefingTemplate;
        $tmp->name = $name;
        $tmp->slug = $slug;
        $tmp->company_id = session('currentCompany');
        $tmp->created_by = Auth::user()->id;
        $tmp->updated_by = Auth::user()->id;
        $tmp->save();
    
        $msg = trans('textos.plantilla_creada');
        return redirect()->route('briefing-templates.edit', $tmp->id)->with(compact('msg'));
    }

    /**
     * 3. Editar template.
     */
    public function edit(BriefingTemplate $template, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.briefing_plantilla_editar',
            'module' => $this->module,
            'slug' => 'briefing-templates'
        ];  

        $items = BriefingTemplateItem::where('template_id', $template->id)
        ->get();

        //Bloques para items:
        $blocks = BriefingTemplateItem::where('template_id', $template->id)
        ->whereNull('recid')
        ->pluck('name', 'id')
        ->toArray();

        //Unidades de tiempo:
        $time_units = helpers::timeUnits();

        $languages = $this->languages;

        return view('admin.briefing_templates.edit')->with(compact('header', 'template', 'tab', 'items', 'blocks', 'time_units', 'languages'));      
    }

    /**
     * 4. Actualizar template.
     */
    public function update(Request $request, BriefingTemplate $template){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/briefing-templates/'.$template->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $name = Str::lower($request->name);
        $slug = Str::slug($request->name);

        $template->name = $name;
        $template->slug = $slug;
        $template->conditions = $request->conditions;
        $template->status = $request->status? true:false;
        $template->updated_by = Auth::user()->id;
        $template->save();

        $msg = trans('textos.plantilla_updated');
        return redirect()->route('briefing-templates.edit', $template->id)->with(compact('msg'));
    }

    /**
     * 5. Eliminar template.
     */
    public function destroy(BriefingTemplate $template){
        $template->delete();

        $msg = trans('textos.plantilla_deleted');
        return redirect()->route('briefing-templates.index')->with(compact('msg'));
    }
}
