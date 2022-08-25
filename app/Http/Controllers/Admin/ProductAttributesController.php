<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Attribute;
use App\Models\ProductAttributes;

class ProductAttributesController extends Controller{
    /**
     * 1. Listado de atributos por producto para DataTable.
     * 2. Guardar atributo de producto.
     * 3. Actualizar atributo de producto.
     * 4. Eliminar atributo de producto.
     */

    private $option = 'catalogo';  
    private $module = 'products';
    private $data_types;

    public function __construct(){
        //Tipos de datos:
        $this->data_types = helpers::dataTypes();

        $this->middleware('module_setted:products');
        $this->middleware('product_post', ['only' => ['store', 'update']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'product_id' => 'required|integer'
                //'attributes' => 'required'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'product_id' => 'required|integer',
                'id' => 'required|integer'
            ]);
        }
    } 

    /**
     * 1. Listado de atributos por producto para DataTable.
     */
    public function getAttributesDT($product_id){
        $data = Attribute::select('attributes.*', 'product_attributes.id AS paId', 'product_attributes.value')
        ->join('product_attributes', 'attributes.id', '=', 'product_attributes.attribute_id')
        ->where('product_attributes.product_id', $product_id)
        ->orderBy('attributes.name', 'ASC');

        $data_types = $this->data_types;

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return ucfirst($row->name);
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
        ->addColumn('value', function($row) use($data_types){
            $d = $data_types[$row->datatype];
            if($d[2] == 'select'){
                return str_replace(',', ', ', $row->value);
            }elseif($d[2] == 'boolean'){
                if($row->value == 1){
                    return ucfirst(trans('textos.verdadero'));
                }else{
                    return ucfirst(trans('textos.falso'));    
                }
            }elseif($d[2] == 'color'){
                $colors = '';
                $arrColors = $row->values? explode(',', $row->value):false;
                if($arrColors){    
                    foreach($arrColors as $ac){
                        $colors .= '<span class="colorEx mini mr-1" style="margin-top: 5px; background-color: '.$ac.'"></span>';
                    }
                }
                return $colors;
            }else{
                return $row->value;
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.product_attributes.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['value', 'datatype'])
        ->make(true);
    }

    /**
     * 2. Guardar atributo de producto.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/products/'.$request->product_id.'/edit/attributes')
                ->withErrors($validator)
                ->withInput();
        }   

        foreach($request->input('attributes') as $at){
            $pa = new ProductAttributes();
            $pa->product_id = $request->product_id;
            $pa->attribute_id = $at;
            $pa->save(); 
        }

        $msg = trans('textos.atributos_vinculados');
        return redirect()->route('products.edit', [$request->product_id, 'attributes'])->with(compact('msg'));
    }

    /**
     * 3. Actualizar atributo de producto.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-edit-attribute');

            return redirect('/admin/products/'.$request->product_id.'/edit/attributes')
                ->withErrors($validator)
                ->withInput();
        }  

        $pa = ProductAttributes::find($request->id);

        //Validación del producto:
        if($pa->product_id != $request->product_id){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('products.index')->with(compact('alert'));
            exit;    
        }

        //Extraemos el valor declarado según el tipo de dato del atributo:
        $value = NULL;
        $attribute = Attribute::find($pa->attribute_id);

        if($this->data_types[$attribute->datatype][2] == 'select'){
            //Convertimos array en string:
            $value = implode(',', $request->value_select);
        }elseif($this->data_types[$attribute->datatype][2] == 'color'){
            //Convertimos array en string:
            $value = $request->value_color? implode(',', $request->value_color):NULL;
        }else{
            $r = 'value_'.$this->data_types[$attribute->datatype][2];
            $value = $request->input($r);
        }

        $pa->value = $value;
        $pa->save();

        $msg = trans('textos.atributo_updated');
        return redirect()->route('products.edit', [$request->product_id, 'attributes'])->with(compact('msg'));
    }

    /**
     * 4. Eliminar atributo de producto.
     */
    public function destroy(ProductAttributes $attribute){
        $attribute->delete();

        $msg = trans('textos.atributo_deleted');
        return redirect()->route('products.edit', [$attribute->product_id, 'attributes'])->with(compact('msg'));
    }
}
