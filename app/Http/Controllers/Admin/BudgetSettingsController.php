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
use App\Models\BudgetSettings;

class BudgetSettingsController extends Controller{
    /**
     * 1. Templates de configuración de presupuestos.
     * 1.1. Listado de templates para DataTable.
     * 2. Formulario nuevo template.
     * 3. Guardar nuevo template.
     * 4. Editar template.
     * 5. Actualizar template.
     * 6. Eliminar template.
     * 7. Actualizar estado de template.
     * 8. Marcar template principal.
     */
    
    private $option = 'presupuestos';
    private $module = 'budgets';
    private $languages; 

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:budgets');
        $this->middleware('budget_settings_get', ['only' => ['edit', 'update', 'destroy', 'featured']]);
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
     * 1. Templates de configuración de presupuestos.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.presupuestos_configuracion',
            'module' => $this->module,
            'slug' => 'budget-settings'
        ];  

        return view('admin.budget_settings.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de templates para DataTable.
     */
    public function getTemplatesDT(){
        $templates = BudgetSettings::where('company_id', session('currentCompany'))->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('budget-settings.edit');
        $eliminar = Auth::user()->can('budget-settings.destroy');

        //Ampliación de información de los objetos:
        foreach($templates as $row){
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($templates)->toJson();
    }

    /**
     * 2. Formulario nuevo template.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.plantilla_nueva',
            'module' => $this->module,
            'slug' => 'budget-settings'
        ];  

        return view('admin.budget_settings.create')->with(compact('header'));
    }

    /**
     * 3. Guardar nuevo template.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/budget-settings/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->input('status')? 1:0;

        //Marcamos como template principal si no existen:
        $templates = BudgetSettings::select('id')
        ->where('company_id', session('currentCompany'))
        ->get();
        $featured = !$templates->count() > 0? 1:0;

        $template = new BudgetSettings();
        $template->name = $request->name;
        $template->slug = $slug;
        $template->company_id = session('currentCompany');
        $template->conditions = $request->conditions;
        $template->payment_method = $request->payment_method;
        $template->observations = $request->observations;
        $template->footer_legend = $request->footer_legend;
        $template->featured = $featured;
        $template->status = $status;
        $template->save();

        $msg = trans('textos.plantilla_creada');
        return redirect()->route('budget-settings.index')->with(compact('msg'));
    }

    /**
     * 4. Editar template.
     *
     * @param  \App\BudgetSettings $template
     * @return \Illuminate\Http\Response
     */
    public function edit(BudgetSettings $template){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.plantilla_editar',
            'module' => $this->module,
            'slug' => 'budget-settings'
        ]; 

        return view('admin.budget_settings.edit')->with(compact('header', 'template'));
    }

    /**
     * 5. Actualizar template.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BudgetSettings $template){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/budget-settings/'.$template->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->input('status')? 1:0;

        $template->name = $request->input('name');
        $template->slug = $slug;
        $template->conditions = $request->input('conditions');
        $template->payment_method = $request->input('payment_method');
        $template->observations = $request->input('observations');
        $template->footer_legend = $request->input('footer_legend');
        $template->status = $status;
        $template->save();

        $msg = trans('textos.plantilla_updated');
        return redirect()->route('budget-settings.edit', $template->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar template.
     *
     * @param  BudgetSettings $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(BudgetSettings $template){
        $template->delete();

        $msg = trans('textos.plantilla_deleted');
        return redirect()->route('budget-settings.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado de template.
     */
    public function status(Request $request, BudgetSettings $template, $status){
        if($request->ajax()){       
            $template->status = $status;
            $template->save();

            $resp = ($template)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 8. Marcar template principal.
     */
    public function featured(BudgetSettings $template){
        BudgetSettings::where('company_id', session('currentCompany'))
          ->update(['featured' => 0]);

        $template->featured = 1;
        $template->save();

        $msg = trans('textos.plantilla_updated');
        return redirect()->route('budget-settings.index')->with(compact('msg'));
    }
}


