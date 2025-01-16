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
use App\Models\Briefing;
use App\Models\BriefingItem;
use App\Models\BriefingTemplate;
use App\Models\BriefingTemplateItem;
use App\Models\Company;
use App\Models\Order;

class BriefingController extends Controller{
    /**
     * 1. Relación de briefings por empresa.
     * 1.1. Listado de briefings para DataTable.
     * 2. Formulario nuevo briefing.
     * 3. Guardar nuevo briefing.
     * 4. Editar briefing.
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
     * 1. Relación de briefings por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.briefings',
            'module' => $this->module,
            'slug' => 'briefings'
        ];  

        return view('admin.briefings.index')->with(compact('header'));  
    }

    /**
     * 1.1. Listado de briefings para DataTable.
     */
    public function getBriefingsDT(){
        $data = Briefing::select('briefings.*', 'companies.name AS customer')
        ->leftJoin('companies', 'briefings.customer_id', '=', 'companies.id')
        ->where('briefings.company_id', session('currentCompany'))
        ->orderBy('briefings.created_at', 'DESC');

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('customer', function($row){
            return $row->customer;
        })
        ->addColumn('date', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('acciones', function($row){
            return view('admin.briefings.partials.dt-actions', compact('row'));
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevo briefing.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.briefing_nuevo',
            'module' => $this->module,
            'slug' => 'briefings'
        ]; 

        $templates = BriefingTemplate::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();  

        return view('admin.briefings.create')->with(compact('header', 'templates'));   
    }

    /**
     * 3. Guardar nuevo briefing.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/briefings/create')
                ->withErrors($validator)
                ->withInput();
        }
        
        $name = Str::lower($request->name);
        $slug = Str::slug($request->name);

        $briefing = new Briefing();
        $briefing->name = $name;
        $briefing->slug = $slug;
        $briefing->company_id = session('currentCompany');
        $briefing->customer_id = $request->customer_id;
        $briefing->template_id = $request->template;
        $briefing->observations = $request->observations;
        $briefing->created_by = Auth::user()->id;
        $briefing->updated_by = Auth::user()->id;
        $briefing->save();

        //Si viene declarado el template, copiamos los items del template a items del briefing.
        if($briefing->template_id){
            $template_items = BriefingTemplateItem::where('template_id', $briefing->template_id)
            ->get();
            if($template_items->count()){
                foreach($template_items as $ti){
                    $bi = new BriefingItem();
                    $bi->concept = $ti->name;
                    $bi->briefing_id = $briefing->id;
                    $bi->template_item_id = $ti->id;
                    $bi->time_qty = $ti->expected_qty;
                    $bi->time_unit = $ti->time_unit;
                    $bi->created_by = Auth::user()->id;
                    $bi->updated_by = Auth::user()->id;
                    $bi->save();
                }
            }
        }

        $msg = trans('textos.briefing_creado');
        return redirect()->route('briefings.edit', $briefing->id)->with(compact('msg'));
    }

    /**
     * 4. Editar briefing.
     */
    public function edit(Briefing $briefing, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.briefing_editar',
            'module' => $this->module,
            'slug' => 'briefings'
        ]; 

        $templates = BriefingTemplate::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray(); 

        //Pedido:
        $order = $briefing->order_id? Order::select('id', 'is_budget', 'is_order', 'order_num', 'order_old_num', 'budget_num', 'budget_old_num')->find($briefing->order_id):false;

        //Items:
        $items = BriefingItem::where('briefing_id', $briefing->id)
        ->get();

        //Unidades de tiempo:
        $time_units = helpers::timeUnits();

        $languages = $this->languages;

        return view('admin.briefings.edit')->with(compact('header', 'briefing', 'tab', 'templates', 'order', 'items', 'time_units', 'languages'));
    }

    /**
     * 5. Actualizar briefing.
     */
    public function update(Request $request, Briefing $briefing){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/briefings/'.$briefing->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $name = Str::lower($request->name);
        $slug = Str::slug($request->name);

        $briefing->name = $name;
        $briefing->slug = $slug;
        $briefing->observations = $request->observations;
        $briefing->updated_by = Auth::user()->id;
        $briefing->save();

        $msg = trans('textos.briefing_updated');
        return redirect()->route('briefings.edit', $briefing->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar briefing.
     */
    public function destroy(Briefing $briefing){
        $briefing->delete();

        $msg = trans('textos.briefing_eliminado');
        return redirect()->route('briefings.index')->with(compact('msg'));
    }
}
