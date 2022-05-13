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
use App\Models\Contract;

class ContractController extends Controller{
	/**
	 * 1. Contratos por empresa.
	 * 1.1. Listado de contratos para DataTable.
	 * 2. Formulario nuevo contrato.
	 * 3. Guardar nuevo contrato.
	 * 4. Editar contrato.
     * 5. Mostrar contrato.
     * 6. Actualizar contrato.
     * 7. Eliminar contrato.
     * 8. Actualizar estado.
	 */
	
	private $option = 'contratos';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
        $this->middleware('contract_get', ['only' => ['edit', 'show', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);

        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);    
        }
    }

    /**
     * 1. Contratos por empresa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
    	$header = [
            'option' => $this->option, 
            'suboption' => 'textos.contratos_mis',
            'module' => $this->module,
            'slug' => 'contracts'
        ]; 

        return view('admin.contracts.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de contratos para DataTable.
     */
    public function getContractsDT(){
    	$contracts = Contract::where('company_id', session('currentCompany'))->get();

    	//Permisos del usuario:
        $editar = Auth::user()->can('contracts.edit');
        $eliminar = Auth::user()->can('contracts.destroy');

    	foreach($contracts as $row){
            //Enviamos las fechas ya formateadas:
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            $duration = intval($row->duration) == config('constants.CONTRATO_INDEFINIDO_DURACION_')? ucfirst(trans('textos.indefinido')):$row->duration;
            $row->duration = $duration;

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;
        }

    	return datatables($contracts)->toJson();
    }

    /**
     * 2. Formulario nuevo contrato.
     */
    public function create(){
    	$header = [
            'option' => $this->option, 
            'suboption' => 'textos.contrato_nuevo',
            'module' => $this->module,
            'slug' => 'contracts'
        ]; 

        return view('admin.contracts.create')->with(compact('header'));	
    }

    /**
     * 3. Guardar nuevo contrato.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/contracts/create')
                ->withErrors($validator)
                ->withInput();
        } 

        $slug = Str::slug($request->name);
        $duration = $request->undefined? config('constants.CONTRATO_INDEFINIDO_DURACION_'):$request->duration;  
        $with_holidays = $request->with_holidays? 1:0;
        $status = $request->status? 1:0;

        $contract = new Contract;
        $contract->name = strtolower($request->name);
        $contract->slug = $slug;
        $contract->company_id = session('currentCompany');
        $contract->duration = $duration;
        $contract->with_holidays = $with_holidays;
        $contract->status = $status;
        $contract->save();

        $msg = trans('textos.contrato_creado_ok');
        return redirect()->route('contracts.index')->with(compact('msg'));       
    }

    /**
     * 4. Editar contrato.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Contract $contract){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.contrato_editar',
            'module' => $this->module,
            'slug' => 'contracts'
        ]; 

        return view('admin.contracts.edit', compact('header', 'contract'));      
    }

    /**
     * 5. Mostrar contrato.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $contract){
    	$header = [
            'option' => $this->option, 
            'suboption' => 'textos.contrato',
            'module' => $this->module,
            'slug' => 'contracts'
        ];     

        return view('admin.contracts.show', compact('header', 'contract'));
    }

    /**
     * 6. Actualizar contrato.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contract $contract){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/contracts/'.$contract->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        } 

        $slug = Str::slug($request->name);
        $duration = $request->undefined? config('constants.CONTRATO_INDEFINIDO_DURACION_'):$request->duration;  
        $status = $request->status? 1:0;

        $contract->name = strtolower($request->name);
        $contract->slug = $slug;
        $contract->duration = $duration;
        $contract->status = $status;
        $contract->save();

        $msg = trans('textos.contrato_updated');
        return redirect()->route('contracts.edit', $contract->id)->with(compact('msg'));  
    }

    /**
     * 7. Eliminar contrato.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract){
        $contract->delete();  

        $msg = trans('textos.contrato_deleted');
        return redirect()->route('contracts.index')->with(compact('msg'));   
    }

    /**
     * 8. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $contract = Contract::find($id);
            $contract->status = $status;
            $contract->save();

            $resp = ($contract)? true:false;
            return response()->json($resp);
        }
    }
}
