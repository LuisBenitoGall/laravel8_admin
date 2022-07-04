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
use App\Models\BudgetFormItems;
use App\Models\BudgetForms;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Product;

class BudgetFormsController extends Controller{
    /**
     * 1. Listado de formularios por empresa.
     * 1.1. Listado de formularios para DataTable.
     * 2. Guardar nuevo formulario.
     * 3. Editar formulario.
     * 4. Actualizar formulario.
     * 5. Activar / desactivar formulario.
     * 6. Eliminar formulario.
     *
     *
     *
     * 11. Setear formulario para cliente.
     */
    
    private $option = 'presupuestos';
    private $module = 'budgets';
    private $languages;
    

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:budgets');
        $this->middleware('budget_form_get', ['only' => ['edit', 'update', 'destroy']]);
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
     * 1. Listado de formularios por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.formularios_mis',
            'module' => $this->module,
            'slug' => 'budget-forms'
        ];  

        return view('admin.budget_forms.index')->with(compact('header'));  
    }

    /**
     * 1.1. Listado de formularios para DataTable.
     */
    public function getBudgetFormsDT(){
        $forms = BudgetForms::where('company_id', session('currentCompany'))->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('budget-forms.edit');
        $eliminar = Auth::user()->can('budget-forms.destroy');

        //Ampliación de información de los objetos:
        foreach($forms as $row){
            //Enviamos las fechas ya formateadas:
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($forms)->toJson();
    }

    /**
     * 2. Guardar nuevo formulario.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-form');
            
            return redirect('budget-forms')
                ->withErrors($validator)
                ->withInput();
        }

        $name = Str::lower($request->name);
        $slug = Str::slug($request->name);

        $form = new BudgetForms;
        $form->name = $name;
        $form->slug = $slug;
        $form->company_id = session('currentCompany');
        $form->save();
    
        $msg = trans('textos.formulario_creado_ok');
        return redirect()->route('budget-forms.edit', $form->id)->with(compact('msg'));
    }

    /**
     * 3. Editar formulario.
     */
    public function edit(BudgetForms $form, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.formulario_editar',
            'module' => $this->module,
            'slug' => 'budget-forms'
        ];  

        $blocks = BudgetFormItems::where('recid', '0')
        ->where('form_id', $form->id)
        ->get();

        //Productos del formulario.
        $products_in = BudgetFormItems::where('form_id', $form->id)
        ->whereNotNull('product_id')
        ->pluck('product_id')
        ->toArray();

        //Sólo los productos que no pertenezcan al formulario, activos y en venta.
        $products = Product::select('id', 'name', 'translations', 'price', 'calculated_by_percent')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->where('on_sale', 1)
        ->whereNotIn('id', $products_in)
        ->orderBy('name', 'ASC')
        ->get();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $languages = $this->languages;

        return view('admin.budget_forms.edit')->with(compact('header', 'form', 'tab', 'blocks', 'products', 'currency', 'languages'));
    }

    /**
     * 3.1. Listado de clientes por formulario para DataTable.
     */
    public function getBudgetFormCustomersDT(){
        //Clientes:
        $customers = CustomerProviders::getCompanyCustomers(session('currentCompany'));  

        //Ampliación de información de los objetos:
        if($customers->count()){
            //Permisos del usuario:
            $editar = Auth::user()->can('budget-forms.edit');

            foreach($customers as $row){
                //Permisos para las acciones:
                $row->editar = $editar;
            }

            return datatables($customers)->toJson();
        
        }else{
            return false;
        }           
    }

    /**
     * 4. Actualizar formulario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BudgetForms $form){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/budget-forms/'.$form->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $name = Str::lower($request->name);
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;

        $arrConditions = [];
        foreach($this->languages as $lang){
            $condition = 'conditions_'.$lang[0];
            $arrConditions[$lang[0]] = $request->input($condition);
        }
        $conditions = !empty($arrConditions)? serialize($arrConditions):NULL;

        $form->name = $name;
        $form->slug = $slug;
        $form->conditions = $conditions;
        $form->status = $status;
        $form->save();

        $msg = trans('textos.formulario_updated');
        return redirect()->route('budget-forms.edit', $form->id)->with(compact('msg'));
    }

    /**
     * 5. Activar / desactivar formulario.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $form = BudgetForms::find($id);
            $form->status = $status;
            $form->save();

            $resp = ($form)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 6. Eliminar formulario.
     */
    public function destroy(BudgetForms $form){
        $form->delete();  

        $msg = trans('textos.formulario_deleted');
        return redirect()->route('budget-forms.index')->with(compact('msg'));   
    }







    /**
     * 11. Setear formulario para cliente.
     */
    public function setCustomerForm(Request $request, $id, $form, $status){
        if($request->ajax()){
            $bfi = $status == 1? $form:NULL;

            //dd($bfi, $status);

            CustomerProviders::where('provider_id', session('currentCompany'))
            ->where('customer_id', $id)
            ->update(['budget_form_id' => $bfi]);
          
            return response()->json(true);
        }
    }
}
