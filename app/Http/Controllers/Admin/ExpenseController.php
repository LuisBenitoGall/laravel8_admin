<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Expense;
use App\Models\Project;

class ExpenseController extends Controller{
    /**
     * 1. Gastos por empresa.
     * 2. Listado de gastos para DataTable.
     * 3. Formulario nuevo gasto.
     * 4. Guardar nuevo gasto.
     * 5. Editar gasto.
     * 6. Actualizar gasto.
     */

    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;
    private $co_currency;
    private $co_iva;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        //IVA:
        $this->co_iva = CompanySettings::co_iva();

        $this->middleware('module_setted:accounting');
        $this->middleware('expense_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'concept' => 'required|max:255',
                'amount' => 'nullable|numeric',
                'iva' => 'nullable|numeric',
                'amount_with_iva' => 'nullable|numeric',
                'type' => 'nullable|integer',
                'project_id' => 'nullable|integer',
                'provider_id' => 'nullable|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'concept' => 'required|max:255',
                'amount' => 'nullable|numeric',
                'iva' => 'nullable|numeric',
                'amount_with_iva' => 'nullable|numeric',
                'type' => 'nullable|integer',
                'project_id' => 'nullable|integer',
                'provider_id' => 'nullable|integer'
            ]);
        }
    } 

    /**
     * 1. Gastos por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.gastos',
            'module' => $this->module,
            'slug' => 'expenses'
        ];  

        return view('admin.expenses.index')->with(compact('header'));    
    }

    /**
     * 2. Listado de gastos para DataTable.
     */
    public function getExpensesDT(){
        $data = Expense::where('company_id', session('currentCompany'));   
        
        return DataTables::of($data)
        ->addColumn('num', function($row){
            return view('admin.orders.partials.dt-num', compact('order')); 
        }) 


        ->rawColumns(['order_parent'])
        ->make(true);
    }

    /**
     * 3. Formulario nuevo gasto.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.gasto_nuevo',
            'module' => $this->module,
            'slug' => 'expenses'
        ];  

        //Tipos de pago:
        $types = helpers::expenseTypes(); 

        //Proyectos:
        $projects = Project::select(DB::raw("CONCAT(project_num, ' - ', name) AS project"), 'id')
        ->where('company_id', session('currentCompany'))
        ->whereNotNull('project_num')
        ->pluck('project', 'id')
        ->toArray();

        //Proveedores:
        $providers = Company::join('customer_providers', 'companies.id', '=', 'customer_providers.provider_id')
        ->where('customer_providers.customer_id', session('currentCompany'))
        ->where('companies.status', 1)
        ->where('customer_providers.status', 1)
        ->pluck('companies.name', 'companies.id')
        ->toArray();

        $currency = $this->co_currency;

        $iva = $this->co_iva;

        $languages = $this->languages;

        return view('admin.expenses.create')->with(compact('header', 'types', 'projects', 'providers', 'currency', 'iva', 'languages'));    
    }

    /**
     * 4. Guardar nuevo gasto.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/expenses/create')
            ->withErrors($validator)
            ->withInput();    
        }

        $expensed_in = $request->input('published_at') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('published_at')):$request->input('published_at'):NULL;

        $row = new Expense();
        $row->company_id = session('currentCompany');
        $row->concept = $request->concept;
        $row->amount = $request->amount;
        $row->iva = $request->iva;
        $row->amount_with_iva = $request->amount_with_iva;
        $row->expensed_in = $expensed_in;
        $row->type = $request->type;
        $row->project_id = $request->project_id;
        $row->provider_id = $request->provider_id;
        $row->observations = $request->observations;
        $row->created_by = Auth::user()->id;
        $row->doc = $doc;
        $row->save();

        $msg = trans('textos.gasto_agregado');
        return redirect()->route('expenses.edit', $row)->with(compact('msg'));
    }

    /**
     * 5. Editar gasto.
     */
    public function edit(Expense $expense){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.gasto_editar',
            'module' => $this->module,
            'slug' => 'expenses'
        ];  

        //Tipos de pago:
        $types = helpers::expenseTypes(); 

        //Proyectos:
        $projects = Project::select(DB::raw("CONCAT(project_num, ' - ', name) AS project"), 'id')
        ->where('company_id', session('currentCompany'))
        ->whereNotNull('project_num')
        ->pluck('project', 'id')
        ->toArray();

        //Proveedores:
        $providers = Company::join('customer_providers', 'companies.id', '=', 'customer_providers.provider_id')
        ->where('customer_providers.customer_id', session('currentCompany'))
        ->where('companies.status', 1)
        ->where('customer_providers.status', 1)
        ->pluck('companies.name', 'companies.id')
        ->toArray();

        $currency = $this->co_currency;

        $iva = $this->co_iva;

        $languages = $this->languages;

        return view('admin.expenses.edit')->with(compact('header', 'expense', 'types', 'projects', 'providers', 'currency', 'iva', 'languages')); 
    }

    /**
     * 6. Actualizar gasto.
     */
    public function update(Request $request, Expense $expense){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/expenses/'.$expense->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }
    }
}
