<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\BusinessAreas;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProducts;
use App\Models\Product;

class CustomerProductsController extends Controller{
    /**
     * 1. Listado de productos por cliente.
     * 1.1. Listado de productos por proveedor.
     * 1.2. Listado de productos para DataTable.
     * 1.3. Listado de productos por filtro.
     * 2. Activar - desactivar producto para cliente.
     * 3. Actualizar precio producto para cliente.
     */  
    
    private $option = 'productos';
    private $module = 'products';

    public function __construct(){
        $this->middleware('module_setted:products');
        $this->middleware('customer_provider', ['only' => ['index']]);
        $this->middleware('customer_post', ['only' => ['getProductsBy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación search:
        if(isset($data['submit_search'])){
            return Validator::make($data, [
                'customer_id' => 'required|integer',
                'ba_id' => 'nullable|integer'
            ]); 
        }
    }
    
    /**
     * 1. Listado de productos por cliente.
     */
    public function index(Company $customer, $ba = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.catalogo_cliente',
            'module' => $this->module,
            'slug' => 'products'
        ]; 

        $provider = false;

        //Areas de negocio del cliente:
        $areas = BusinessAreas::where('company_id', $customer->id)->get();

        $business_area = $ba? BusinessAreas::find($ba):false;

        return view('admin.customer_products.index')->with(compact('header', 'customer', 'provider', 'business_area', 'areas'));
    }

    /**
     * 1.1. Listado de productos por proveedor.
     */
    public function providerCatalogue(Company $provider){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.catalogo_proveedor',
            'module' => $this->module,
            'slug' => 'products'
        ]; 

        $customer = false;

        //Areas de negocio del cliente:
        $areas = BusinessAreas::where('company_id', $provider->id)->get();

        $business_area = $ba? BusinessAreas::find($ba):false;

        return view('admin.customer_products.index')->with(compact('header', 'customer', 'provider', 'business_area', 'areas'));
    }

    /**
     * 1.2. Listado de productos para DataTable.
     *
     * @param  $customer_id customer
     * @param  $ba_id business_area
     * @param  $cc_id cost_center
     */
    public function getProductsDT($customer_id, $ba_id = false, $cc_id = false){
        $products = Product::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->orderBy('name', 'ASC')
        ->get();

        if($products->count() > 0){
            $arr_product_id = [];
            $arr_ids = [];
            $arr_price = [];
            if($customer_id){
                if($ba_id) $ba_id = trim($ba_id);

                if($ba_id){
                    $customer_products = CustomerProducts::select('id', 'product_id', 'price')
                    ->where('provider_id', session('currentCompany'))
                    ->where('customer_id', $customer_id)
                    ->where('ba_id', $ba_id)
                    ->get();
                }else{
                    $customer_products = CustomerProducts::select('id', 'product_id', 'price')
                    ->where('provider_id', session('currentCompany'))
                    ->where('customer_id', $customer_id)
                    ->whereNull('ba_id')
                    ->get();   
                }
                
                if($customer_products->count() > 0){
                    foreach($customer_products as $cp){
                        array_push($arr_product_id, $cp->product_id);
                        $arr_ids[$cp->product_id] = $cp->id;
                        $arr_price[$cp->product_id] = $cp->price;
                    }
                }
            }

            //Moneda:
            $currency = CompanySettings::co_currency(0);

            //Ampliación de información de los objetos:
            foreach($products as $row){
                //Enviamos el nombre del producto ya traducido:
                $trans = unserialize($row->translations);
                $producto = (isset($trans[session()->get('locale')]) && $trans[session()->get('locale')] != '' && $trans != '')? $trans[session()->get('locale')]:$row->name;
                $row->producto = ucfirst($producto);

                //Descripción:
                $description = unserialize($row->description);
                $descripcion = (isset($description[session()->get('locale')]) && $description[session()->get('locale')] != '' && $description != '')? $description[session()->get('locale')]:$row->name;
                $row->descripcion = ucfirst($descripcion);

                $row->currency = $currency->symbol;

                //Producto activo. Le pasamos el customer_products id para poder eliminar la línea y el precio exclusivo que tenga.
                if(in_array($row->id, $arr_product_id)){
                    $row->active = true;
                    $row->cp_id = $arr_ids[$row->id];
                    $row->price = $arr_price[$row->id];
                }else{
                    $row->active = false;
                    $row->cp_id = false;
                }
            }
        }

        return datatables($products)->toJson();
    }

    /**
     * 1.3. Listado de productos por filtro.
     */
    public function getProductsBy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/customer_products/'.$request->customer_id)
                ->withErrors($validator)
                ->withInput();
        }

        return redirect()->route('customer-products.index', [$request->customer_id, $request->ba_id]);
    }

    /**
     * 2. Activar - desactivar producto para cliente.
     */
    public function status(Request $request, $customer_id, $id, $status, $price, $ba_id = false){
        if($request->ajax()){ 
            //Insertamos o eliminamos en función del status recibido:
            if($status == 1){
                $ba_id = $ba_id? $ba_id:NULL;

                $cp = new CustomerProducts();  
                $cp->product_id = $id;
                $cp->provider_id = session('currentCompany'); 
                $cp->customer_id = $customer_id;
                $cp->ba_id = $ba_id;
                //$cp->cc_id = '';
                $cp->price = $price;
                $cp->save();
            }else{
                $query = CustomerProducts::where('product_id', $id)
                ->where('provider_id', session('currentCompany'))
                ->where('customer_id', $customer_id);
                //Filtro business_area:
                if($ba_id){
                    $query->where('ba_id', $ba_id);
                }else{
                    $query->whereNull('ba_id');
                }
                $data = $query->get();
                foreach($data as $row){
                    $row->delete();    
                }
                $cp = true;
            }     

            $resp = ($cp)? true:false;
            return response()->json($resp);
        }    
    }

    /**
     * 3. Actualizar precio producto para cliente.
     */
    public function updatePrice(Request $request, $customer_id, $id, $price, $ba_id = false){
        if($request->ajax()){ 
            if($customer_id && is_numeric($price)){
                if($ba_id){
                    CustomerProducts::where('product_id', $id)
                    ->where('provider_id', session('currentCompany'))
                    ->where('customer_id', $customer_id)
                    ->where('ba_id', $ba_id)
                    ->update(['price' => $price]);  
                }else{
                    CustomerProducts::where('product_id', $id)
                    ->where('provider_id', session('currentCompany'))
                    ->where('customer_id', $customer_id)
                    ->whereNull('ba_id')
                    ->update(['price' => $price]);    
                }
                $resp = true;
            }else{
                $resp = false;
            }
            return response()->json($resp);
        }
    }
  
}
