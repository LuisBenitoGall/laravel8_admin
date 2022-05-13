<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\BusinessAreas;
use App\Models\Company;

class BusinessAreasController extends Controller{
    /**
     * 1. Listado de áreas de negocio por empresa.
     * 1.1. Listado de áreas de negocio para DataTable.
     * 2. Formulario para nueva área de negocio.
     * 3. Guardar nuevo área de negocio.
     * 4. Editar área de negocio.
     * 5. Actualizar área de negocio.
     * 6. Eliminar área de negocio.
     * 7. Actualizar estado.
     * 8. Histórico de costes por área de negocio y período.
     */   
    
    private $option = 'empresas';  
    private $module = 'companies';
    private $business_relationship = 'companies';

    public function __construct(){
        $this->middleware('company_ajax', ['only' => ['getBusinessAreasDT']]);
        $this->middleware('company_get3', ['only' => ['index']]);
        $this->middleware('business_area_get', ['only' => ['edit', 'update', 'destroy']]);
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
     * 1. Listado de áreas de negocio por empresa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($company_id = false, $business_relationship = false){
        $companyId = $company_id? $company_id:session('currentCompany');

        if($business_relationship == 'customers'){
            $this->option = 'clientes';
        }elseif($business_relationship == 'providers'){
            $this->option = 'proveedores';
        }else{
            $business_relationship = $this->business_relationship;
        }     

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.areas_negocio',
            'module' => $this->module,
            'slug' => 'companies-business-areas'
        ];

        $company = Company::find($companyId);

        return view('admin.business_areas.index')->with(compact('header', 'company', 'business_relationship'));    
    }

    /**
     * 1.1. Listado de áreas de negocio para DataTable.
     */
    public function getBusinessAreasDT($company_id, $business_relationship){
        $business_areas = BusinessAreas::where('company_id', $company_id)
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('business-areas.edit');
        $eliminar = Auth::user()->can('business-areas.destroy');

        if($business_areas->count() && $business_relationship){
            foreach($business_areas as $row){
                $row->br = $business_relationship;

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar; 
            }
        }

        return datatables($business_areas)->toJson(); 
    }

    /**
     * 2. Formulario para nueva área de negocio.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($company_id = false, $business_relationship = false){
        $companyId = $company_id? $company_id:session('currentCompany');

        if($business_relationship == 'customers'){
            $this->option = 'clientes';
        }elseif($business_relationship == 'providers'){
            $this->option = 'proveedores';
        }else{
            $business_relationship = $this->business_relationship;
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.area_negocio_nueva',
            'module' => $this->module,
            'slug' => 'companies-business-areas'
        ];    

        $previous_url = url()->previous();

        $company = Company::find($companyId);

        return view('admin.business_areas.create')->with(compact('header', 'previous_url', 'company', 'business_relationship'));
    }

    /**
     * 3. Guardar nuevo área de negocio.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/business-areas/'.$request->company_id.'/'.$request->business_relationship.'/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status'))? 1:0;
        $company_id = $request->company_id? $request->company_id:session('currentCompany');

        $business_area = new BusinessAreas();
        $business_area->company_id = $company_id;
        $business_area->name = $request->input('name');
        $business_area->slug = $slug;
        $business_area->status = $status;
        $business_area->save();

        $msg = trans('textos.area_negocio_creada');
        return redirect()->route('business-areas.edit', [$business_area->id, $request->business_relationship])->with(compact('msg'));
    }

    /**
     * 4. Editar área de negocio.
     */
    public function edit(BusinessAreas $business_area, $business_relationship = false){
        if($business_relationship == 'customers'){
            $this->option = 'clientes';
        }elseif($business_relationship == 'providers'){
            $this->option = 'proveedores';
        }else{
            $business_relationship = $this->business_relationship;
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.area_negocio_editar',
            'module' => $this->module,
            'slug' => 'companies-business-areas'
        ];

        $company = Company::select('id', 'name')->find($business_area->company_id);

        return view('admin.business_areas.edit', compact('header', 'business_area', 'company', 'business_relationship'));
    }

    /**
     * 5. Actualizar área de negocio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessAreas $business_area){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/business-areas/'.$business_area->id.'/'.$request->business_relationship.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status'))? 1:0;

        $business_area->name = $request->input('name');
        $business_area->slug = $slug;
        $business_area->status = $status;
        $business_area->save();

        $msg = trans('textos.area_negocio_updated');
        return redirect()->route('business-areas.edit', [$business_area->id, $request->business_relationship])->with(compact('msg'));
    }

    /**
     * 6. Eliminar área de negocio.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, BusinessAreas $business_area){
        $company_id = $business_area->company_id;

        $business_area->delete();  

        $msg = trans('textos.area_negocio_deleted');
        return redirect()->route('business-areas.index', [$company_id, $request->business_relationship])->with(compact('msg'));   
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $business_area = BusinessAreas::find($id);
            $business_area->status = $status;
            $business_area->save();

            $resp = ($business_area)? true:false;
            return response()->json($resp);
        }
    }
}
