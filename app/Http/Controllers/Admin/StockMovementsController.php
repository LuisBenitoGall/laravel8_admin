<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\StockMovements;

class StockMovementsController extends Controller{
    /**
     * 1. Listado de movimientos de stock.
     * 1.1. Listado de movimientos para DataTable.
     * 2. Formulario nuevo movimiento stock.
     * 3. Guardar nuevo movimiento.
     * 4. Editar movimiento de stock.
     * 5. Actualizar movimiento de stock.
     * 6. Eliminar movimiento de stock.
     */

    private $option = 'configuracion';
    private $module = 'settings';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('stock_movement_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'acronym' => 'required|max:3',
                'sign' => 'required'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);
        }
    } 

    /**
     * 1. Listado de movimientos de stock.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.stock_movimientos',
            'module' => $this->module,
            'slug' => 'stock-movements'
        ];   

        return view('admin.stock_movements.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de movimientos para DataTable.
     */
    public function getMovementsDT(){
        //26/09/2022: se estandarizan los movimientos de stock para todas las empresas. Los movimientos son los mismos para todas las empresas y se gestionan como configuración del ERP por el SuperAdmin.
        // $data = StockMovements::where('basic', 1)
        // ->orWhere('company_id', session('currentCompany'));
        
        $data = StockMovements::orderBy('name', 'ASC')->get();

        $signs = helpers::signStockMovements();

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return ucfirst($row->name);
        })
        ->addColumn('acronym', function($row){
            return '<span class="text-uppercase">'.$row->acronym.'</span>';
        })
        ->addColumn('sign', function($row) use($signs){
            return ucfirst($signs[$row->sign]);
        })
        ->addColumn('explicacion', function($row){
            $explanations = $row->explanation? unserialize($row->explanation):false;
            if($explanations){

            }else{
                return '';
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.stock_movements.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['acronym'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo movimiento stock.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.movimiento_nuevo',
            'module' => $this->module,
            'slug' => 'stock-movements'
        ];   

        $signs = helpers::signStockMovements();

        $languages = $this->languages;

        //Permitir guardar el movimiento como básico, sólo activo para SuperAdmin:
        //$roles = auth()->user()->roles;
        //$basic = $roles[0]->special == config('constants.ALL_ACCESS_')? true:false;

        return view('admin.stock_movements.create')->with(compact('header', 'signs', 'languages'));
    }

    /**
     * 3. Guardar nuevo movimiento.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/stock-movements/create')
                ->withErrors($validator)
                ->withInput();
        }

        $basic = $request->basic? true:false;

        $arrExplanation = [];
        foreach($this->languages as $lang){
            $explanation = 'explanation_'.$lang[0];
            $arrExplanation[$lang[0]] = $request->input($explanation);
        }
        $strExplanation = serialize($arrExplanation);

        $mov = new StockMovements();
        $mov->name = $request->name;
        //26/09/2022: se estandarizan los movimientos de stock para todas las empresas. Ya no se crean movimiento exclusivos por empresa.
        // if(!$basic){
        //     $mov->company_id = session('currentCompany'); 
        // }
        $mov->acronym = $request->acronym;
        $mov->sign = $request->sign;
        $mov->basic = $basic;
        $mov->explanation = $strExplanation;
        $mov->save();

        $msg = trans('textos.movimiento_creado');
        return redirect()->route('stock-movements.edit', $mov->id)->with(compact('msg'));
    }

    /**
     * 4. Editar movimiento de stock.
     */
    public function edit(StockMovements $movement){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.movimiento_editar',
            'module' => $this->module,
            'slug' => 'stock-movements'
        ];   

        $signs = helpers::signStockMovements();

        $languages = $this->languages;  

        return view('admin.stock_movements.edit')->with(compact('header', 'movement', 'signs', 'languages'));  
    }

    /**
     * 5. Actualizar movimiento de stock.
     */
    public function update(Request $request, StockMovements $movement){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/stock-movements/'.$movement->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }  

        $arrExplanation = [];
        foreach($this->languages as $lang){
            $explanation = 'explanation_'.$lang[0];
            $arrExplanation[$lang[0]] = $request->input($explanation);
        }
        $strExplanation = serialize($arrExplanation);

        $movement->name = $request->name;
        $movement->acronym = $request->acronym;
        $movement->explanation = $strExplanation;
        $movement->save();  

        $msg = trans('textos.movimiento_updated');
        return redirect()->route('stock-movements.edit', $movement)->with(compact('msg'));
    }

    /**
     * 6. Eliminar movimiento de stock.
     */
    public function destroy(StockMovements $movement){
        $movement->delete();

        $msg = trans('textos.movimiento_deleted');
        return redirect()->route('stock-movements.index')->with(compact('msg'));
    }
}
