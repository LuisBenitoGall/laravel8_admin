<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Product;
use App\Models\ProductSerials;
use App\Models\Stock;

class ProductSerialsController extends Controller{
    /**  
     * 1. Listado de números de serie por producto para DataTable.
     * 2. Guardar serials.
     * 3. Eliminar serial.
     * 4. Serials por producto.
     */

    private $option = 'catalogo';  
    private $module = 'products';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:products');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'prefix' => 'nullable|max:255',
                'qty' => 'required|integer'
            ],[
                'qty.integer' => trans('textos.numero_entero_aviso')
            ]);
        }
    } 

    /**
     * 1. Listado de números de serie por producto para DataTable.
     */
    public function getSerialsDT($product_id){
        $data = ProductSerials::where('product_id', $product_id)
        ->orderBy('id', 'ASC');

        return DataTables::of($data)
        ->addColumn('serial', function($row){
            return ucfirst($row->serial);
        })
        ->addColumn('alta', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);    
        })
        ->addColumn('employee', function($row){
            $user = $row->user();
            if($user && $user->operation == 's'){
                return $user->name.' '.$user->surname;
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.product_serials.partials.dt-actions', compact('row'));    
        })
        ->make(true);
    }

    /**  
     * 2. Guardar serials.
     */
    public function store(Request $request, Product $product){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-serials');

            return redirect('/admin/products/'.$product->id.'/edit/serials')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->qty > 0){
            $i = 0;
            while($i < $request->qty){
                $serial = new ProductSerials();
                $serial->product_id = $product->id;
                $serial->save();

                $num = $request->prefix? $request->prefix.'-'.$serial->id:$serial->id;

                $serial->serial = $num;
                $serial->save();

                $i++;
            }
        }

        $msg = trans('textos.serializacion_creada');
        return redirect()->route('products.edit', [$product->id, 'serials'])->with(compact('msg'));
    }
}
