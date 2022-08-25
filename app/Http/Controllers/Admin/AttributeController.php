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
use Carbon\Carbon;

//Models:
use App\Models\Attribute;

class attributeController extends Controller{
    /**
     * 1. Listado de atributos.
     * 1.1. Listado de atributos para DataTable.
     * 2. Formulario nuevo atributo.
     * 3. Guardar nuevo atributo.
     * 4. Editar atributo.
     * 5. Actualizar atributo.
     * 6. Actualizar estado.
     * 7. Eliminar atributo.
     */

    private $option = 'catalogo';  
    private $module = 'products';
    private $languages;  
    private $data_types;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Tipos de datos:
        $this->data_types = helpers::dataTypes();

        $this->middleware('module_setted:products');
        $this->middleware('attribute_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'datatype' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);
        }
    }

    /**
     * 1. Listado de atributos.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.atributos',
            'module' => $this->module,
            'slug' => 'attributes'
        ]; 
        
        return view('admin.attributes.index')->with(compact('header'));    
    }

    /**
     * 1.1. Listado de atributos para DataTable.
     */
    public function getattributesDT(){
        $data = Attribute::where('company_id', session('currentCompany')) 
        ->orderBy('name', 'ASC'); 

        $data_types = $this->data_types;

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return ucfirst($row->name);
        })  
        ->addColumn('description', function($row){
            return ucfirst($row->description);
        })  
        ->addColumn('datatype', function($row) use($data_types){
            $d = $data_types[$row->datatype];
            $datatype = $d[0];
            if($row->values){
                //Tipo Color:
                if($row->datatype == 60){
                    $arrColors = $row->values? explode(' ', $row->values):false;
                    if($arrColors){
                        $datatype .= ' ( ';
                        foreach($arrColors as $ac){
                            if($ac != ''){
                                $p = explode('-', $ac); 
                                $datatype .= '<span class="colorEx mini mr-1" style="margin-top: 5px; background-color: '.$p[0].'"></span>';   
                            }
                        }
                        $datatype .= ')';
                    }
                }else{
                    $datatype .= ' ('.str_replace(',', ', ', $row->values).')';
                }
            }
            return $datatype;
        })
        ->addColumn('acciones', function($row){
            return view('admin.attributes.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['description', 'datatype'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo atributo.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.atributo_nuevo',
            'module' => $this->module,
            'slug' => 'attributes'
        ]; 

        $data_types = $this->data_types;

        return view('admin.attributes.create')->with(compact('header', 'data_types'));    
    }

    /**
     * 3. Guardar nuevo atributo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/attributes/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Tipo de dato:
        $values = $this->data_types[$request->datatype][2] == 'color' && $request->values_colors? trim($request->values_colors):$request->values;

        //Quitamos dobles espacios en blanco:
        $values = str_replace('  ', ' ', $values);

        $p = new Attribute();
        $p->name = $request->name;
        $p->slug = Str::slug($request->name); 
        $p->description = $request->description;
        $p->datatype = $request->datatype;
        $p->values = $values;
        $p->company_id = session('currentCompany');
        $p->status = $request->status? 1:'0';
        $p->save();

        $msg = trans('textos.atributo_creado');
        return redirect()->route('attributes.edit', $p->id)->with(compact('msg'));
    }

    /**
     * 4. Editar atributo.
     */
    public function edit(attribute $attribute){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.atributo_editar',
            'module' => $this->module,
            'slug' => 'attributes'
        ]; 

        $data_types = $this->data_types;

        return view('admin.attributes.edit')->with(compact('header', 'attribute', 'data_types'));   
    }

    /**
     * 5. Actualizar atributo.
     */
    public function update(Request $request, Attribute $attribute){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/attributes/'.$attribute->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }  

        //Tipo de dato:
        $values = $this->data_types[$request->datatype][2] == 'color' && $request->values_colors? trim($request->values_colors):$request->values; 

        //Quitamos dobles espacios en blanco:
        $values = str_replace('  ', ' ', $values);

        $attribute->name = $request->input('name');
        $attribute->slug = Str::slug($request->name);
        $attribute->description = $request->input('description');
        $attribute->values = $values;
        $attribute->status = $request->status? 1:'0';
        $attribute->save();

        $msg = trans('textos.atributo_updated');
        return redirect()->route('attributes.edit', $attribute->id)->with(compact('msg'));
    }

    /**
     * 6. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $attribute = Attribute::find($id);
            $attribute->status = $status;
            $attribute->save();

            $resp = ($attribute)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar atributo.
     */
    public function destroy(Attribute $attribute){
        $attribute->delete();

        $msg = trans('textos.atributo_deleted');
        return redirect()->route('attributes.index')->with(compact('msg'));       
    }
}
