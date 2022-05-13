<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Company;
use App\Models\CostCenters;

class CostCentersController extends Controller{
    /**
     * 1. Listado de centros de coste por empresa.
     * 1.1. Listado de centros de coste para DataTable.
     * 2. Formulario para nuevo centro de coste.
     * 3. Guardar nuevo centro de coste.
     * 4. Editar centro de coste.
     * 5. Actualizar centro de coste.
     * 6. Eliminar centro de coste.
     * 7. Actualizar estado.
     */
    
    private $option = 'empresas';  
    private $module = 'companies';
    private $business_relationship = 'companies';

    public function __construct(){
        $this->middleware('company_ajax', ['only' => ['getCostCentersDT']]);
        $this->middleware('company_get3', ['only' => ['index']]);
        $this->middleware('cost_center_get', ['only' => ['edit', 'update', 'destroy']]);
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
     * 1. Listado de centros de coste por empresa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($company_id = false, $business_relationship = false){
        $companyId = $company_id? $company_id:session('currentCompany');

        if($business_relationship == 'customers'){
            $this->option = 'clientes';
        }elseif($business_relationship == 'providers'){
            $this->option = 'proveedores';
        }elseif(!$business_relationship){
            $business_relationship = $this->business_relationship;
        }    

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.centros_coste',
            'module' => $this->module,
            'slug' => 'companies-cost-centers'
        ];

        $company = Company::find($companyId);

        return view('admin.cost_centers.index')->with(compact('header', 'company', 'business_relationship'));    
    }

    /**
     * 1.1. Listado de centros de coste para DataTable.
     */
    public function getCostCentersDT($company_id, $business_relationship){
        $costcenters = CostCenters::where('company_id', $company_id)
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('cost_centers.edit');
        $eliminar = Auth::user()->can('cost_centers.destroy');

        if($costcenters->count() && $business_relationship){
            foreach($costcenters as $row){
                $row->br = $business_relationship;

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar; 
            }
        }

        return datatables($costcenters)->toJson(); 
    }

    /**
     * 2. Formulario para nuevo centro de coste.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($company_id = false, $business_relationship = false){
        if($business_relationship == 'customers'){
            $this->option = 'clientes';
        }elseif($business_relationship == 'providers'){
            $this->option = 'proveedores';
        }else{
            $business_relationship = $this->business_relationship;
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.centro_coste_nuevo',
            'module' => $this->module,
            'slug' => 'companies-cost-centers'
        ];    

        $previous_url = url()->previous();

        $company = $company_id? Company::find($company_id):Company::find(session('currentCompany'));

        return view('admin.cost_centers.create')->with(compact('header', 'previous_url', 'company', 'business_relationship'));
    }

    /**
     * 3. Guardar nuevo centro de coste.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/cost-centers/'.$request->company_id.'/'.$request->business_relationship.'/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status'))? 1:0;
        $company_id = $request->company_id? $request->company_id:session('currentCompany');

        $cost_center = new CostCenters();
        $cost_center->company_id = $company_id;
        $cost_center->name = $request->input('name');
        $cost_center->slug = $slug;
        $cost_center->status = $status;
        $cost_center->save();

        $msg = trans('textos.centro_creado');
        return redirect()->route('cost-centers.edit', [$cost_center->id, $request->business_relationship])->with(compact('msg'));
    }

    /**
     * 4. Editar centro de coste.
     */
    public function edit(CostCenters $cost_center, $business_relationship = false){
        if($business_relationship == 'customers'){
            $this->option = 'clientes';
        }elseif($business_relationship == 'providers'){
            $this->option = 'proveedores';
        }else{
            $business_relationship = $this->business_relationship;
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.centro_coste_editar',
            'module' => $this->module,
            'slug' => 'companies-cost-centers'
        ];

        $company = Company::select('id', 'name')->find($cost_center->company_id);

        return view('admin.cost_centers.edit', compact('header', 'cost_center', 'company', 'business_relationship'));
    }

    /**
     * 5. Actualizar centro de trabajo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CostCenters $cost_center){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/cost-centers/'.$cost_center->id.'/'.$request->business_relationship.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status'))? 1:0;

        $cost_center->name = $request->input('name');
        $cost_center->slug = $slug;
        $cost_center->status = $status;
        $cost_center->save();

        $msg = trans('textos.centro_coste_updated');
        return redirect()->route('cost-centers.edit', [$cost_center->id, $request->business_relationship])->with(compact('msg'));
    }

    /**
     * 6. Eliminar centro de coste.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, CostCenters $cost_center){
        $company_id = $cost_center->company_id;

        $cost_center->delete();  

        $msg = trans('textos.centro_coste_deleted');
        return redirect()->route('cost-centers.index', [$company_id, $request->business_relationship])->with(compact('msg'));   
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $cost_center = CostCenters::find($id);
            $cost_center->status = $status;
            $cost_center->save();

            $resp = ($cost_center)? true:false;
            return response()->json($resp);
        }
    }

}
