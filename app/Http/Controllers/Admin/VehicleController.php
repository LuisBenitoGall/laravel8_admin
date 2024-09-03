<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

//Models:
use App\Models\Vehicle;
use App\Models\VehicleImages;

class VehicleController extends Controller{
    /**
     * 1. Flota de vehículos.
     * 1.1. Flota de vehículos para DataTable.
     * 2. Formulario nuevo vehículo.
     * 3. Guardar nuevo vehículo.
     * 4. Editar vehículo.
     * 5. Actualizar vehículo.
     * 6. Actulizar estado de vehículo.
     * 7. Eliminar vehículo.
     */
    
    private $option = 'vehiculos';
    private $module = 'logistics';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:logistics');
        $this->middleware('vehicle_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'type' => 'nullable|integer',
                'brand' => 'nullable|integer',
                'car_license' => 'nullable|max:25',
                'model' => 'nullable|max:150',
                'base_weight' => 'nullable|numeric',
                'max_weight' => 'nullable|required_with:base_weight|numeric|greater_than_field:base_weight',
                'chassis_number' => 'nullable|max:100'
            ],[
                'max_weight.greater_than_field' => trans('textos.peso_max_no_menor')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'type' => 'nullable|integer',
                'brand' => 'nullable|integer',
                'car_license' => 'nullable|max:25',
                'model' => 'nullable|max:150',
                'base_weight' => 'nullable|required_with:max_weight|numeric',
                'max_weight' => 'nullable|required_with:base_weight|numeric|greater_than_field:base_weight',
                'chassis_number' => 'nullable|max:100'
            ],[
                'base_weight.required_with' => trans('textos.tara_requerida'),
                'max_weight.required_with' => trans('textos.peso_max_requerido'),
                'max_weight.greater_than_field' => trans('textos.peso_max_no_menor')
            ]);
        }
    }

    /**
     * 1. Flota de vehículos.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.flota',
            'module' => $this->module,
            'slug' => 'vehicles'
        ]; 

        return view('admin.vehicles.index')->with(compact('header'));
    }

    /**
     * 1.1. Flota de vehículos para DataTable.
     */
    public function getVehiclesDT(){
        $data = Vehicle::select('vehicles.*',
        DB::raw('(SELECT image FROM vehicle_images WHERE vehicle_id = vehicles.id AND featured = 1) AS Image')
        )
        ->where('vehicles.company_id', session('currentCompany'))
        ->orderBy('vehicles.name', 'ASC')
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('vehicles.edit');
        $eliminar = Auth::user()->can('vehicles.destroy');

        $types = helpers::vehicleTypes();
        $brands = helpers::vehicleBrands();

        foreach($data as $row){
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            $row->type = $row->vehicle_type? $types[$row->vehicle_type]:'';
            $row->marca = $row->brand? $brands[$row->brand]:'';

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($data)->toJson();
    }

    /**
     * 2. Formulario nuevo vehículo.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.vehiculo_nuevo',
            'module' => $this->module,
            'slug' => 'vehicles'
        ]; 

        $types = helpers::vehicleTypes();
        asort($types);
        $brands = helpers::vehicleBrands();
        asort($brands);
        $languages = $this->languages;

        return view('admin.vehicles.create')->with(compact('header', 'types', 'brands', 'languages'));         
    }

    /**
     * 3. Guardar nuevo vehículo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/vehicles/create')
            ->withErrors($validator)
            ->withInput();    
        }

        $status = $request->status? 1:0;

        //Formateando fechas:
        $discharge_date = $request->discharge_date != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->discharge_date):$request->discharge_date:NULL;

        $vehicle = new Vehicle();
        $vehicle->company_id = session('currentCompany');
        $vehicle->name = $request->name;
        $vehicle->vehicle_type = $request->type;
        $vehicle->brand = $request->brand;
        $vehicle->model = $request->model;
        $vehicle->car_license = $request->car_license;
        $vehicle->discharge_date = $discharge_date;
        $vehicle->vehicle_base_weight = $request->base_weight;
        $vehicle->max_weight = $request->max_weight;
        $vehicle->chassis_number = $request->chassis_number;
        $vehicle->observations = $request->observations;
        $vehicle->status = $status;
        $vehicle->save();

        $msg = trans('textos.vehiculo_creado');
        return redirect()->route('vehicles.edit', $vehicle)->with(compact('msg'));
    }

    /**
     * 4. Editar vehículo.
     */
    public function edit(Vehicle $vehicle, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.vehiculo_editar',
            'module' => $this->module,
            'slug' => 'vehicles'
        ]; 

        $types = helpers::vehicleTypes();
        asort($types);
        $brands = helpers::vehicleBrands();
        asort($brands);

        $images = VehicleImages::where('vehicle_id', $vehicle->id)->get();

        $languages = $this->languages;

        return view('admin.vehicles.edit')->with(compact('header', 'vehicle', 'tab', 'types', 'brands', 'images', 'languages')); 
    }

    /**
     * 5. Actualizar vehículo.
     */
    public function update(Request $request, Vehicle $vehicle){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/vehicles/'.$vehicle->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        $status = $request->status? 1:0;

        //Formateando fechas:
        $discharge_date = $request->discharge_date != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->discharge_date):$request->discharge_date:NULL;

        $vehicle->name = $request->name;
        $vehicle->vehicle_type = $request->type;
        $vehicle->brand = $request->brand;
        $vehicle->model = $request->model;
        $vehicle->car_license = $request->car_license;
        $vehicle->discharge_date = $discharge_date;
        $vehicle->vehicle_base_weight = $request->base_weight;
        $vehicle->max_weight = $request->max_weight;
        $vehicle->chassis_number = $request->chassis_number;
        $vehicle->observations = $request->observations;
        $vehicle->status = $status;
        $vehicle->save();

        $msg = trans('textos.vehiculo_updated');
        return redirect()->route('vehicles.edit', $vehicle)->with(compact('msg'));
    }

    /**
     * 6. Actulizar estado de vehículo.
     */
    public function status(Request $request, Vehicle $vehicle, $status){
        if($request->ajax()){    
            $vehicle->status = $status;
            $vehicle->save();

            return response()->json(true);
        }
    }

    /**
     * 7. Eliminar vehículo.
     */
    public function destroy(Vehicle $vehicle){
        $vehicle->delete();
        
        $msg = trans('textos.vehiculo_deleted');
        return redirect()->route('vehicles.index')->with(compact('msg'));    
    }
}
