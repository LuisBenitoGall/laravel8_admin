<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\Company;
use App\Models\CustomerProviders;
use App\Models\Phone;
use App\Models\Workplace;

class WorkplaceController extends Controller{
    /**
     * 1. Listado de centros de trabajo por empresa.
     * 1.1. Listado de centros de trabajo para DataTable.
     * 2. Formulario para nuevo centro de trabajo.
     * 3. Guardar nuevo centro de trabajo.
     * 4. Editar centro de trabajo.
     * 5. Actualizar centro de trabajo.
     * 6. Eliminar centro de trabajo.
     * 7. Actualizar estado.
     * 8. Eliminar logo.
     * 9. Centro de trabajo por defecto.
     * 10. Marcar centro principal.
     */  
    
    private $option = 'empresas';  
    private $module = 'companies';  
    private $business_relationship = 'companies';

    public function __construct(){
        //18/10/22: revisar uso de estos middlewares para edición de clientes.
        //$this->middleware('company_ajax', ['only' => ['getWorkplacesDT']]);
        //$this->middleware('company_get3', ['only' => ['index']]);
        //$this->middleware('workplace_get', ['only' => ['edit', 'update', 'destroy', 'byDefault', 'featured']]);
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
                'name' => 'required|string|max:255',
                'town_id' => 'nullable|integer',
                'nif' => "nullable|string|max:15|unique:workplaces,nif,".$data['id'].",id,deleted_at,NULL"
            ]);
        }
    } 

    /**
     * 1. Listado de centros de trabajo por empresa.
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
            'suboption' => 'textos.centros_trabajo',
            'module' => $this->module,
            'slug' => 'companies-workplaces'
        ];

        $company = Company::find($companyId);

        return view('admin.workplaces.index')->with(compact('header', 'company', 'business_relationship'));    
    }

    /**
     * 1.1. Listado de centros de trabajo para DataTable.
     */
    public function getWorkplacesDT($company_id, $business_relationship){
        $workplaces = Workplace::select('workplaces.*', 'towns.name AS Town')
        ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
        ->where('workplaces.company_id', $company_id)
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('workplaces.edit');
        $eliminar = Auth::user()->can('workplaces.destroy');

        if($workplaces->count() && $business_relationship){
            foreach($workplaces as $row){
                $row->br = $business_relationship;

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar; 
            }
        }

        return datatables($workplaces)->toJson(); 
    }

    /**
     * 2. Formulario para nuevo centro de trabajo.
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
            'suboption' => 'textos.centro_trabajo_nuevo',
            'module' => $this->module,
            'slug' => 'companies-workplaces'
        ];    

        $previous_url = url()->previous();

        $company = Company::find($companyId);

        return view('admin.workplaces.create')->with(compact('header', 'previous_url', 'company', 'business_relationship'));
    }

    /**
     * 3. Guardar nuevo centro de trabajo.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/workplaces/'.$request->company_id.'/'.$request->business_relationship.'/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status'))? 1:0;
        $company_id = $request->company_id? $request->company_id:session('currentCompany');

        //Nº de workplaces existentes para marcar como principal:
        $workplaces = Workplace::select('id')
        ->where('company_id', $company_id)
        ->get();

        $featured = $workplaces->count() == 0? 1:0;

        $workplace = new Workplace();
        $workplace->name = $request->input('name');
        $workplace->slug = $slug;
        $workplace->featured = $featured;
        $workplace->company_id = $company_id;;
        $workplace->status = $status;
        $workplace->save();

        $msg = trans('textos.centro_creado');
        return redirect()->route('workplaces.edit', [$workplace->id, $request->business_relationship])->with(compact('msg'));
    }

    /**
     * 4. Editar centro de trabajo.
     */
    public function edit($id, $business_relationship = false){
        if($business_relationship == 'customers'){
            $this->option = 'clientes';
        }elseif($business_relationship == 'providers'){
            $this->option = 'proveedores';
        }else{
            $business_relationship = $this->business_relationship;
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.centro_trabajo_editar',
            'module' => $this->module,
            'slug' => 'companies-workplaces'
        ];   
        $provinces = [];
        $towns = [];

        $workplace = Workplace::select('workplaces.*', 'provinces.country_id', 'towns.province_id')
        ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
        ->leftJoin('provinces', 'towns.province_id', '=', 'provinces.id')
        ->find($id);

        $company = Company::find($workplace->company_id);

        //Países:
        $countries = $this->countries(1);
        //Provincias:
        if($workplace->province_id){
            $provinces = $this->countryProvinces($workplace->country_id, 1, true);
        }
        //Poblaciones:
        if($workplace->town_id){
            $towns = $this->provinceTowns($workplace->province_id, 1, true);
        }

        //Teléfonos:
        $phones = Phone::where('workplace_id', $id)->get();

        //Página sin cambio de empresa:
        $noSelectCompany = false;   //'noSelectCompany',

        //Pasamos a session la empresa editada:
        //session(['currentCompany' => $company->id]);

        return view('admin.workplaces.edit', compact('header', 'workplace', 'company', 'provinces', 'towns', 'countries', 'phones', 'business_relationship'));  
    }

    /**
     * 5. Actualizar centro de trabajo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Workplace $workplace){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/workplaces/'.$workplace->id.'/'.$request->business_relationship.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        $status = ($request->input('status'))? 1:0;

        //Subir logo:
        $fileName = $this->loadLogo($request, 'workplaces');

        $workplace->name = $request->input('name');
        $workplace->slug = $slug;
        if($fileName){
            $workplace->logo = $fileName;
        }
        $workplace->address = $request->input('address');
        $workplace->cp = $request->input('cp');
        if($request->input('town_id')){
            $workplace->town_id = $request->input('town_id');
        }
        //04/11/2020: se oculta el nif para los centros de trabajo, pues el nif sólo es por empresa:
        //$workplace->nif = $request->input('nif');
        $workplace->website = $request->input('website');
        $workplace->description = $request->input('description');
        $workplace->status = $status;
        $workplace->save();
        
        $msg = trans('textos.centro_updated');
        return redirect()->route('workplaces.edit', [$workplace->id, $request->business_relationship])->with(compact('msg'));
    }

    /**
     * 6. Eliminar centro de trabajo.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Workplace $workplace){
        $company_id = $workplace->company_id;

        File::delete(public_path().'/img/workplaces/'.$workplace->logo);

        $workplace->delete();  

        $msg = trans('textos.centro_deleted');
        return redirect()->route('workplaces.index', [$company_id, $request->business_relationship])->with(compact('msg'));   
    }

    /**
     * 7. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $workplace = Workplace::find($id);
            $workplace->status = $status;
            $workplace->save();

            $resp = ($workplace)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 8. Eliminar logo.
     *
     * @param  obj $request string $logo
     * @return \Illuminate\Http\Response
     */
    public function deleteLogo(Request $request, $logo, $business_relationship = false){
        if($logo != ''){
            $workplace = Workplace::select('id')->where('logo', $logo)->first();

            if(!$workplace){
                return redirect('/');
                exit;    
            }

            File::delete(public_path().'/img/workplaces/'.$logo);

            $workplace->logo = NULL;
            $workplace->save();

            $business_relationship = $business_relationship? $business_relationship:'companies';

            $msg = trans('textos.imagen_deleted');
            return redirect()->route('workplaces.edit', [$workplace->id, $business_relationship])->with(compact('msg'));  
        
        }else{
            return redirect('/');
        } 
    }

    /**
     * 9. Centro de trabajo por defecto.
     */
    public function byDefault(Workplace $workplace){
        dd($workplace);
        $data = CustomerProviders::where('customer_id', $workplace->company_id)
        ->where('provider_id', session('currentCompany'))
        ->update(['customer_workplace_id' => $workplace->id]);

        $msg = trans('textos.centro_updated');
        return redirect()->route('workplaces.index')->with(compact('msg'));
    }

    /**
     * 10. Marcar centro principal.
     */
    public function featured(Workplace $workplace, $business_relationship){
        Workplace::where('company_id', $workplace->company_id)
          ->update(['featured' => 0]);

        $workplace->featured = 1;
        $workplace->save();

        $msg = trans('textos.centro_updated');
        return redirect()->route('workplaces.index', [$workplace->company_id, $business_relationship])->with(compact('msg'));
    }
}
