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
use App\Models\AccountingConcept;

class AccountingConceptController extends Controller{
    /**
     * 1. Conceptos contables por empresa.
     * 1.1. Listado de conceptos.
     * 2. Formulario nuevo concepto.
     * 3. Guardar nuevo concepto.
     * 4. Editar concepto.
     * 5. Actualizar concepto.
     * 6. Actualizar estado.
     * 7. Eliminar concepto.
     * 8. Exportar valores a otras empresas vinculadas.
     * 9. Obtener subconceptos por concepto principal.
     */
    
    private $option = 'conceptos_contables';
    private $module = 'accounting';

    public function __construct(){
        $this->middleware('accounting_concept_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'main' => 'nullable|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'main' => 'nullable|integer'
            ]);
        }
    } 

    /**
     * 1. Conceptos contables por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.listado',
            'module' => $this->module,
            'slug' => 'accounting-concepts'
        ];   

        return view('admin.accounting_concepts.index')->with(compact('header'));   
    }

    /**
     * 1.1. Listado de conceptos.
     */
    public function getConceptsDT(){
        $data = AccountingConcept::where('company_id', session('currentCompany'));

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return ucfirst($row->name);
        })
        ->addColumn('level', function($row){
            $level = trans('textos.concepto_general');
            if($row->main > 0){
                $x = $row->getConceptName($row->main);
                $level = trans('textos.cuenta_gastos').' <b>'.ucfirst($x).'</b>';    
            }
            return $level;
        })
        ->addColumn('description', function($row){
            return ucfirst($row->description);
        })
        ->addColumn('acciones', function($row){
            return view('admin.accounting_concepts.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['level', 'description'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo concepto.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.concepto_nuevo',
            'module' => $this->module,
            'slug' => 'accounting-concepts'
        ];  
        
        //Obtenemos los conceptos principales:
        $concepts = AccountingConcept::where('company_id', session('currentCompany'))
        ->where('main', '0')
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.accounting_concepts.create')->with(compact('header', 'concepts'));  
    }

    /**
     * 3. Guardar nuevo concepto.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/accounting-concepts')
                ->withErrors($validator)
                ->withInput();
        }

        $ac = new AccountingConcept();
        $ac->company_id = session('currentCompany');
        $ac->name = $request->name;
        $ac->slug = Str::slug($request->name);
        $ac->description = $request->description;
        $ac->main = $request->main? $request->main:'0';
        $ac->status = $request->status? 1:'0';
        $ac->save();

        $msg = trans('textos.concepto_creado_ok');
        return redirect()->route('accounting-concepts.index')->with(compact('msg'));
    }

    /**
     * 4. Editar concepto.
     */
    public function edit(AccountingConcept $concept){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.concepto_editar',
            'module' => $this->module,
            'slug' => 'accounting-concepts'
        ];  
        
        //Obtenemos los conceptos principales:
        $concepts = AccountingConcept::where('company_id', session('currentCompany'))
        ->where('main', '0')
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.accounting_concepts.edit')->with(compact('header', 'concept', 'concepts'));  
    }

    /**
     * 5. Actualizar concepto.
     */
    public function update(Request $request, AccountingConcept $concept){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/accounting-concepts/'.$concept->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        } 

        $concept->name = $request->name;
        $concept->slug = Str::slug($request->name);
        $concept->main = $request->main? $request->main:'0';
        $concept->description = $request->description;
        $concept->status = $request->status? 1:'0';
        $concept->save();  

        $msg = trans('textos.concepto_updated');
        return redirect()->route('accounting-concepts.edit', $concept)->with(compact('msg')); 
    }

    /**
     * 6. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $concept = AccountingConcept::find($id);
            $concept->status = $status;
            $concept->save();

            $resp = ($concept)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar concepto.
     */
    public function destroy(AccountingConcept $concept){
        $concept->delete();

        $msg = trans('textos.concepto_deleted');
        return redirect()->route('accounting-concepts.index')->with(compact('msg'));
    }

    /**
     * 8. Exportar valores a otras empresas vinculadas.
     */
    public function export(Request $request){
        if(is_array($request->companies) && count($request->companies) > 0){
            $data = AccountingConcept::where('company_id', session('currentCompany'))
            ->where('main', '0')
            ->get();

            if($data->count() > 0){
                foreach($request->companies as $co){
                    foreach($data as $row){
                        //Comprobamos que no exista el registro:
                        $exist = AccountingConcept::select('id')
                        ->where('company_id', $co)
                        ->where('name', $row->name)
                        ->first();

                        if(!$exist){
                            $ac = new AccountingConcept();
                            $ac->company_id = $co;
                            $ac->name = $row->name;
                            $ac->slug = $row->slug;
                            $ac->main = '0';
                            $ac->description = $row->description;
                            $ac->status = $row->status;
                            $ac->save();

                            //Obtenemos las subcategorías del concepto original:
                            $subconcepts = AccountingConcept::where('company_id', session('currentCompany'))
                            ->where('main', $row->id)
                            ->get();

                            foreach($subconcepts as $sc){
                                $ac_ = new AccountingConcept();
                                $ac_->company_id = $co;
                                $ac_->name = $sc->name;
                                $ac_->slug = $sc->slug;
                                $ac_->main = $ac->id;
                                $ac_->description = $sc->description;
                                $ac_->status = $sc->status;
                                $ac_->save();    
                            }
                        }
                    }
                }

                $msg = trans('textos.exportacion_ok');
                return redirect()->route('accounting-concepts.index')->with(compact('msg'));

            }else{
                $alert = trans('textos.exportacion_vacia');
            }

        }else{
            $alert = trans('textos.exportacion_vacia');
        }

        return redirect()->route('accounting-concepts.index')->with(compact('alert'));
    }

    /**
     * 9. Obtener subconceptos por concepto principal.
     */
    public function filter(Request $request, $concept){
        if($request->ajax()){
            $data = AccountingConcept::select('id', 'name')
            ->where('main', $concept)
            ->orderBy('name', 'ASC')
            ->get();

            return response()->json($data);
        }
    }
}
