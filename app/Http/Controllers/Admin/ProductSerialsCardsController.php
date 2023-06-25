<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductSerials;
use App\Models\ProductSerialsCards;
use App\Models\Stock;

class ProductSerialsCardsController extends Controller{
    /**
     * 1. Editar ficha de la unidad de producto.
     * 1.1. Listado de parámetros de unidad de producto para DataTable.
     * 2. Actualizar parámetro de la unidad de producto.
     * 3. Eliminar valor del parámetro de la unidad de producto.
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
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación update:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                'product_id' => 'required|integer',
                'product_serial_id' => 'required|integer',
                'id' => 'required|integer'
            ]);
        }
    } 

    /**
     * 1. Editar ficha de la unidad de producto.
     */
    public function edit(ProductSerials $serial){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.ficha_unidad_editar',
            'module' => $this->module,
            'slug' => 'products'
        ];

        $product = Product::find($serial->product_id);

        $cards = ProductSerialsCards::select('product_serials_cards.id', 'product_serials_cards.value', 'attributes.name')
        ->join('attributes', 'product_serials_cards.attribute_id', '=', 'attributes.id')
        ->where('product_serials_cards.product_serial_id', $serial->id)
        ->get();

        $languages = $this->languages;

        return view('admin.product_serials_cards.edit', compact('header', 'serial', 'product', 'cards', 'languages'));
    }

    /**
     * 1.1. Listado de parámetros de unidad de producto para DataTable.
     */
    public function getProductAttributesDT($serial_id){
        $data = Attribute::select('attributes.*', 
            DB::raw('(SELECT product_serials_cards.value FROM product_serials_cards WHERE product_serials_cards.attribute_id = attributes.id AND product_serials_cards.product_serial_id = '.$serial_id.') AS value'), 
            DB::raw('(SELECT product_serials_cards.id FROM product_serials_cards WHERE product_serials_cards.attribute_id = attributes.id AND product_serials_cards.product_serial_id = '.$serial_id.') AS pscId')
        )
        ->where('attributes.company_id', session('currentCompany'))
        ->where('attributes.status', 1)
        ->orderBy('attributes.name', 'ASC')->get();

        $data_types = $this->data_types;

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return ucfirst($row->name);
        })
        ->addColumn('datatype', function($row) use($data_types){
            $d = $data_types[$row->datatype];
            $datatype = $d[0];
            if($row->values){
                $datatype .= ' ('.str_replace(',', ', ', $row->values).')';
            }
            return $datatype;
        })
        ->addColumn('description', function($row){
            return ucfirst($row->description);
        }) 
        ->addColumn('value', function($row){
            return ucfirst($row->value);
        })
        ->addColumn('acciones', function($row){
            return view('admin.product_serials_cards.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['value'])
        ->make(true);
    }

    /**
     * 2. Actualizar parámetro de la unidad de producto.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-attribute');

            return redirect('admin/product-serials-cards/'.$request->product_serial_id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        $value = NULL;
        if($request->value_textarea){
            $value = $request->value_textarea;
        }elseif($request->value_datepicker){
            $value = $request->value_datepicker;
        }elseif($request->value_boolean){
            $value = $request->value_boolean;
        }elseif($request->value_select){
            $data = serialize($request->value_select);
            $value = $data;
        }

        ProductSerialsCards::updateOrCreate(
            ['product_serial_id' => $request->product_serial_id, 'attribute_id' => $request->id],
            ['product_serial_id' => $request->product_serial_id, 'attribute_id' => $request->id, 'value' => $value]
        );

        $msg = trans('textos.atributo_updated');
        return redirect()->route('product-serials-cards.edit', $request->product_serial_id)->with(compact('msg'));
    }

    /**
     * 3. Eliminar valor del parámetro de la unidad de producto.
     */
    public function destroy(ProductSerialsCards $serial){
        $serial->delete();

        $msg = trans('textos.atributo_deleted');
        return redirect()->route('product-serials-cards.edit', $serial->product_serial_id)->with(compact('msg'));
    }
}
