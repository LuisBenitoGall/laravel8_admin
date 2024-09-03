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
use App\Models\Company;
use App\Models\TransportAgency;

class TransportAgencyController extends Controller{
    /**
     * 1. Relación de agencias de transporte.
     * 1.1. Listado de agencias de transporte para DataTable.
     * 2. Formulario nueva agencia de transporte.
     * 3. Guardar nueva agencia de transporte.
     * 4. Editar agencia de transporte.
     * 5. Actualizar agencia de transporte.
     * 6. Modificar status de agencia de transporte.
     * 7. Eliminar agencia de transporte.
     */
    
    private $option = 'transporte_agencias';
    private $module = 'settings';
    private $languages;

    public function __construct(){
        $this->languages = helpers::languages();
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required_without:company_id|max:255',
                'tradename' => 'nullable|max:255',
                'nif' => 'required_without:company_id|validateNif|max:15|unique:companies,nif,NULL,id,deleted_at,NULL',
                'account_id' => 'nullable|numeric',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:500'
            ],[
                'nif.unique' => trans('textos.nif_existe')
            ]);
        }
    } 

    /**
     * 1. Relación de agencias de transporte.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.transporte_agencias'),
            'module' => $this->module,
            'slug' => 'transport-agencies'
        ];

        return view('admin.transport_agencies.index')->with(compact('header'));
    }   

    /**
     * 1.1. Listado de agencias de transporte para DataTable.
     */
    public function getTransportAgenciesDT(){
        $data = Company::select('companies.*', 'transport_agencies.id AS ta_id')
        ->join('transport_agencies', 'companies.id', '=', 'transport_agencies.company_id')
        ->where('transport_agencies.status', 1)
        ->orderBy('companies.name', 'ASC');

        return DataTables::of($data)
        ->addColumn('acciones', function($row){
            return view('admin.transport_agencies.partials.dt-actions', compact('row'));    
        })
        ->make(true);
    }

    /**
     * 2. Formulario nueva agencia de transporte.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.agencia_nueva'),
            'module' => $this->module,
            'slug' => 'transport_agencies'
        ];

        return view('admin.transport_agencies.create')->with(compact('header'));     
    }

    /**
     * 3. Guardar nueva agencia de transporte.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/transport-agencies/create')
            ->withErrors($validator)
            ->withInput();    
        }

        $company = Company::saveCompany($request);

        $agency = new TransportAgency();
        $agency->company_id = $company->id;
        $agency->status = $request->status? true:false;
        $agency->save();

        $msg = trans('textos.transporte_agencia_creada');
        return redirect()->route('transport-agencies.edit', $agency)->with(compact('msg'));
    }

    /**
     * 4. Editar agencia de transporte.
     */
    public function edit(TransportAgency $agency){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.transporte_agencia_editar'),
            'module' => $this->module,
            'slug' => 'transport_agencies'
        ];

        $company = Company::find($agency->company_id);

        return view('admin.transport_agencies.edit')->with(compact('header', 'agency', 'company'));  
    }

    /**
     * 5. Actualizar agencia de transporte.
     */
    public function update(Request $request, TransportAgency $transportAgency){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/transport_agencies/'.$transportAgency->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        $transportAgency->name = $request->name;
        $transportAgency->contact_person = $request->contact_person;
        $transportAgency->phone = $request->phone;
        $transportAgency->email = $request->email;
        $transportAgency->address = $request->address;
        $transportAgency->status = $request->status? true:false;
        $transportAgency->save();

        $msg = trans('textos.transporte_agencia_updated');
        return redirect()->route('transport_agencies.edit', $transportAgency)->with(compact('msg'));
    }

    /**
     * 6. Modificar status de agencia de transporte.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){
            $agency = TransportAgency::find($id);
            $agency->status = $status;
            $agency->save();

            $resp = ($agency)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar agencia de transporte.
     */
    public function destroy(TransportAgency $transportAgency){
        $transportAgency->delete();   

        $msg = trans('textos.transporte_agencia_deleted');
        return redirect()->route('transport_agencies.index')->with(compact('msg')); 
    }
}