<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\ProductUnits;
use App\Models\Unit;

class ProductUnitsController extends Controller{
    /**
     * 1. Listado de unidades de producto para DataTable.
     * 2. Vincular unidad a producto.
     */
    
    private $option = 'catalogo';  
    private $module = 'products';

    public function __construct(){
        $this->middleware('module_setted:products');
    }

    /**
     * 1. Listado de unidades de producto para DataTable.
     */
    public function getUnitsDT($product_id){
        $product_units = ProductUnits::where('product_id', $product_id)
        ->pluck('unit_id')
        ->toArray();

        $units = Unit::where('units.status', 1)
        ->orderBy('units.name', 'ASC')
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('products.edit');

        //Ampliación de información de los objetos:
        foreach($units as $row){
            $row->selected = in_array($row->id, $product_units)? 1:'0';

            //Enviamos el nombre de la unidad ya traducido:
            $trans = unserialize($row->translations);
            $unit = (isset($trans[session()->get('locale')]) && $trans[session()->get('locale')] != '' && $trans != '')? $trans[session()->get('locale')]:$row->name;
            $row->unit = ucfirst($unit);

            //Permisos para las acciones:
            $row->editar = $editar;
        }

        return datatables($units)->toJson();
    }

    /**
     * 2. Vincular unidad a producto.
     */
    public function status(Request $request, $product_id, $unit_id){
        if($request->ajax()){
            //Comprobamos si existe el registro:
            $unit = ProductUnits::where('product_id', $product_id)
            ->where('unit_id', $unit_id)
            ->first();

            if($unit){
                $unit->delete();
            }else{
                $unit = new ProductUnits();
                $unit->product_id = $product_id;
                $unit->unit_id = $unit_id;
                $unit->save();
            }

            return response()->json(true);
        }
    }
}
