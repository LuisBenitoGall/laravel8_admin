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
use App\Models\Store;

class StoreController extends Controller{
    /**
     * 1. Almacenes por empresa.
     * 1.1. Listado de almacenes por empresa para DataTable.
     * 2. Formulario nuevo almacén.
     * 3. Guardar nuevo almacén.
     * 4. Editar almacén.
     * 5. Actualizar almacén. 
     */
    
    private $option = 'stock';
    private $module = 'stocks';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:orders');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:100',
                'type' => 'required|max:1'
            ]);
        }
    }

    /**
     * 1. Almacenes por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.almacenes',
            'module' => $this->module,
            'slug' => 'stores'
        ];   

        return view('admin.stores.index')->with(compact('header'));  
    }

    /**
     * 1.1. Listado de almacenes por empresa para DataTable.
     */
    public function getStoresDT(){
        $data = Store::where('company_id', session('currentCompany'));

        $types = helpers::storeTypes();

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('tipo', function($row) use($types){
            return ucfirst($types[$row->type]);
        })
        ->addColumn('alta', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('acciones', function($row){
            return view('admin.stores.partials.dt-actions', compact('row'));    
        })
        ->make(true);
    }    

    /**
     * 2. Formulario nuevo almacén.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.almacen_nuevo',
            'module' => $this->module,
            'slug' => 'stores'
        ];   

        return view('admin.stores.create')->with(compact('header'));      
    }

    /**
     * 3. Guardar nuevo almacén.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/stores/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = $request->name? Str::slug($request->name):NULL;

        $row = new Store();
        $row->name = $request->name;
        $row->slug = $slug;
        $row->company_id = session('currentCompany');
        $row->observations = $request->observations;
        $row->type = $request->type;
        $row->status = $request->status? 1:'0';
        $row->save();

        $msg = trans('textos.almacen_creado');
        return redirect()->route('stores.index')->with(compact('msg'));
    }

    /**
     * 4. Editar almacén.
     */
    public function edit(Store $store){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.almacen_editar',
            'module' => $this->module,
            'slug' => 'stores'
        ];   

        return view('admin.stores.create')->with(compact('header', 'store'));      
    }

    /**
     * 5. Actualizar almacén.
     */
    public function update(Request $request, Store $store){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/stores/'.$store->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = $request->name? Str::slug($request->name):NULL;

        $store->name = $request->name;
        $store->slug = $slug;
        $store->observations = $request->observations;
        $store->status = $request->status? 1:'0';
        $store->save();

        $msg = trans('textos.almacen_updated');
        return redirect()->route('stores.edit', $store)->with(compact('msg'));
    }
}
    