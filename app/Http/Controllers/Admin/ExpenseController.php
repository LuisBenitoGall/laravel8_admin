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
use Yajra\DataTables\DataTables;
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
     * 7. Eliminar gasto.
     * 8. Eliminar documento.
     * 9. Desvincular gasto de proyecto.
     * 10. Vincular gastos a proyecto.
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
        $this->middleware('expense_get', ['only' => ['edit', 'update', 'destroy', 'destroyDoc', 'unlinkProject']]);
        $this->middleware('project_post', ['only' => ['linkToProject']]);
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

        //Validación expenses:
        }elseif(isset($data['submit_add_expenses'])){
            return Validator::make($data, [
                'expenses' => 'required'
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
        $data = Expense::select('expenses.*', 'companies.name AS company', 'projects.project_num', 'projects.name AS project')
        ->leftJoin('companies', 'expenses.provider_id', '=', 'companies.id')
        ->leftJoin('projects', 'expenses.project_id', '=', 'projects.id')
        ->where('expenses.company_id', session('currentCompany'));

        //Moneda:
        $currency = $this->co_currency;  

        //Tipos de pago:
        $types = helpers::expenseTypes(); 
        
        return DataTables::of($data)
        ->addColumn('concept', function($row){
            return $row->concept;
        })
        ->addColumn('expensed_in', function($row){
            return $row->expensed_in? Carbon::parse($row->expensed_in)->format($this->languages[session()->get('locale')][4]):'';
        })
        ->addColumn('amount', function($row) use($currency){
            if($row->amount_with_iva){
                return $row->amount_with_iva.$currency->symbol;
            }else{
                return '';
            }
        }) 
        ->addColumn('type', function($row) use($types){
            if($row->type){
                return $types[$row->type];
            }else{
                return '';
            }
        })
        ->addColumn('provider', function($row){
            return $row->company;
        }) 
        ->addColumn('project', function($row){
            return $row->project_id;
        }) 
        ->addColumn('doc', function($row){
            if($row->doc){
                return '<a target="_blank" href="/docs/expenses/'.$row->doc.'"><i class="fa fa-file"></i></a>';
            }else{
                return '';
            }
        })  
        ->addColumn('acciones', function($row){
            $return = 'expenses';
            return view('admin.expenses.partials.dt-actions', compact('row', 'return')); 
        }) 
        ->rawColumns(['doc'])
        ->make(true);
    }

    /**
     * 3. Formulario nuevo gasto.
     */
    public function create($project_id = ''){
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
        ->where('companies.id', '!=', session('currentCompany'))
        ->pluck('companies.name', 'companies.id')
        ->toArray();

        $currency = $this->co_currency;

        $iva = $this->co_iva;

        $languages = $this->languages;

        return view('admin.expenses.create')->with(compact('header', 'project_id', 'types', 'projects', 'providers', 'currency', 'iva', 'languages'));    
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

        //Subir documento.
        $doc = $this->loadFile($request, 'expenses');

        $expensed_in = $request->input('expensed_in') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('expensed_in')):$request->input('expensed_in'):NULL;

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

        //Tipos de gasto:
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

        //Subir documento:
        $doc = false;
        if($request->hasFile('doc')){
            $doc = $this->loadFile($request, 'expenses');
        }

        $expensed_in = $request->input('expensed_in') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('expensed_in')):$request->input('expensed_in'):NULL;

        $expense->concept = $request->concept;
        $expense->amount = $request->amount;
        $expense->iva = $request->iva;
        $expense->amount_with_iva = $request->amount_with_iva;
        $expense->expensed_in = $expensed_in;
        $expense->type = $request->type;
        $expense->project_id = $request->project_id;
        $expense->provider_id = $request->provider_id;
        $expense->observations = $request->observations;
        if($doc){
            $expense->doc = $doc;
        }
        $expense->save();

        $msg = trans('textos.gasto_updated');
        return redirect()->route('expenses.edit', $expense)->with(compact('msg'));
    }

    /**
     * 7. Eliminar gasto.
     */
    public function destroy(Expense $expense){
        //Eliminamos documento:
        if($expense->doc){
            File::delete(public_path().'/docs/expenses/'.$expense->doc);
        }
        
        $expense->delete();

        $msg = trans('textos.gasto_deleted');
        return redirect()->route('expenses.index')->with(compact('msg'));
    }

    /**
     * 8. Eliminar documento.
     */
    public function destroyDoc(Expense $expense){
        //Eliminamos documento:
        if($expense->doc){
            File::delete(public_path().'/docs/expenses/'.$expense->doc);
        }

        $expense->doc = NULL;
        $expense->save();

        $msg = trans('textos.documento_deleted');
        return redirect()->route('expenses.edit', $expense)->with(compact('msg'));
    }

    /**
     * 9. Desvincular gasto de proyecto.
     */
    public function unlinkProject(Request $request, Expense $expense){
        if($expense->project_id){
            $project_id = $expense->project_id;

            $expense->project_id = NULL;
            $expense->save();

            $msg = trans('textos.gasto_desvinculado');
            return redirect()->route('projects.edit', [$project_id, 'expenses'])->with(compact('msg'));

        }else{
            return redirect()->route('expenses.index')->with(compact('msg'));    
        }
    }

    /**
     * 10. Vincular gastos a proyecto.
     */
    public function linkToProject(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-expense');

            return redirect('/admin/projects/'.$request->project_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        foreach($request->expenses as $row){
            Expense::where('id', $row)
            ->update(['project_id' => $request->project_id]);   
        }

        $msg = trans('textos.gastos_vinculados');
        return redirect()->route('projects.edit', [$request->project_id, 'expenses'])->with(compact('msg'));
    }
}
