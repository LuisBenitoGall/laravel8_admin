<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\BusinessAreas;
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\CompanySettings;
use App\Models\CustomerProducts;
use App\Models\CustomerProviders;
use App\Models\Product;

class CustomerProductsController extends Controller{
    /**
     * 1. Listado de productos por cliente.
     * 1.1. Listado de productos por proveedor.
     * 1.2. Listado de productos para DataTable.
     * 1.2.1. Listado de productos de proveedor para DataTable.
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
    public function index(CustomerProviders $cp, $ba = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.catalogo_cliente',
            'module' => $this->module,
            'slug' => 'products'
        ]; 

        $customer = Company::find($cp->customer_id);
        $provider = false;

        //Areas de negocio del cliente:
        $areas = BusinessAreas::where('company_id', $cp->customer_id)->get();

        $business_area = $ba? BusinessAreas::find($ba):false;

        //Productos de la empresa en session:
        // $session_products = Product::where('company_id', session('currentCompany'))
        // ->where('type', 'p')
        // ->where('status', 1)
        // ->pluck('name', 'id')
        // ->toArray();

        return view('admin.customer_products.index')->with(compact('header', 'customer', 'provider', 'business_area', 'areas'));
    }

    /**
     * 1.1. Listado de productos por proveedor.
     */
    public function providerCatalogue(CustomerProviders $cp){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.catalogo_proveedor',
            'module' => $this->module,
            'slug' => 'products'
        ]; 

        $customer = false;
        $provider = Company::find($cp->provider_id);

        $areas = [];
        $business_area = false;

        //Obtenemos cuenta del proveedor por si está tutelada por la empresa en session:
        $account = CompanyAccounts::where('company_id', $provider->id)
        ->where('status', 1)
        ->orderBy('end_date', 'DESC')
        ->first();

        //Productos de la empresa en session:
        $session_products = Product::where('company_id', session('currentCompany'))
        ->where('type', 'p')
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        return view('admin.customer_products.index')->with(compact('header', 'customer', 'provider', 'business_area', 'areas', 'account', 'session_products'));
    }

    /**
     * 1.2. Listado de productos para DataTable.
     *
     * @param  $customer_id customer
     * @param  $ba_id business_area
     * @param  $cc_id cost_center
     */
    public function getProductsDT($customer_id, $ba_id = false, $cc_id = false){
        // $data = Product::where('company_id', session('currentCompany'))
        // ->where('status', 1)
        // ->orderBy('name', 'ASC');
        
        $data = Product::companyProducts(session('currentCompany'), $customer_id, true, true, false, true);

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('name', function($row){
            //Enviamos el nombre del producto ya traducido:
            $trans = unserialize($row->translations);
            $producto = (isset($trans[session()->get('locale')]) && $trans[session()->get('locale')] != '' && $trans != '')? $trans[session()->get('locale')]:$row->name;
            return ucfirst($producto);
        })
        ->addColumn('descripcion', function($row){
            $description = unserialize($row->description);
            $descripcion = (isset($description[session()->get('locale')]) && $description[session()->get('locale')] != '' && $description != '')? $description[session()->get('locale')]:$row->name;
            return ucfirst($descripcion); 
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="'.asset('img/products/'.$row->Image).'" width="50"><span style="display: none">1</span>';
            }else{
                return '<i class="fa fa-gift icono"></i><span style="display: none">0</span>';    
            }
        })
        ->addColumn('price', function($row) use($currency){
            if($row->calculated_by_percent > 0){
                return trans('textos.precio_porcentual');
            }else{
                return '<span style="display: none">'.$row->price.'</span>'.$row->price.$currency->symbol;     
            }
        })
        ->addColumn('customer_price', function($row) use($customer_id, $currency){
            //Sólo se pueden modificar productos con precio fijo, no porcentual:
            if(!$row->calculated_by_percent || $row->calculated_by_percent == 0){
                $readonly = $row->priceForCustomer? '':'readonly';
                $price = $row->priceForCustomer? $row->priceForCustomer:$row->price;
                $html = '<span style="display: none">'.$price.'</span>';
                $html .= '<div class="input-group">';
                    $html .= '<input type="text" class="form-control text-right customerProductPrice" data-customer="'.$customer_id.'" data-product="'.$row->id.'" value="'.$price.'" '.$readonly.'>';
                    $html .= '<div class="input-group-append"><span class="input-group-addon">'.$currency->symbol.'</span></div>';
                $html .= '</div>';
                return $html;
            }else{
                return $row->calculated_by_percent.'%';
            } 
        })
        ->addColumn('acciones', function($row){
            //Indica si es tabla de clientes o proveedores.
            $environment = 'customers';
            return view('admin.customer_products.partials.dt-actions', compact('row', 'environment'));    
        })
        ->rawColumns(['image', 'price', 'customer_price'])
        ->make(true);
    }

    /**
     * 1.2.1. Listado de productos de proveedor para DataTable.
     */
    public function getProviderProductsDT($provider_id){
        // $data = Product::where('company_id', $provider_id)
        // ->where('status', 1)
        // ->orderBy('name', 'ASC');   

        $data = Product::companyProducts($provider_id, session('currentCompany'));

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('name', function($row){
            //Enviamos el nombre del producto ya traducido:
            $trans = unserialize($row->translations);
            $producto = (isset($trans[session()->get('locale')]) && $trans[session()->get('locale')] != '' && $trans != '')? $trans[session()->get('locale')]:$row->name;
            return ucfirst($producto);
        })
        ->addColumn('descripcion', function($row){
            $description = unserialize($row->description);
            $descripcion = (isset($description[session()->get('locale')]) && $description[session()->get('locale')] != '' && $description != '')? $description[session()->get('locale')]:$row->name;
            return ucfirst($descripcion); 
        })
        ->addColumn('internal_ref', function($row){
            return 'xxxxx';
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="'.asset('img/products/'.$row->Image).'" width="50"><span style="display: none">1</span>';
            }else{
                return '<i class="fa fa-gift icono"></i><span style="display: none">0</span>';    
            }
        })
        ->addColumn('price', function($row) use($currency){
            if($row->calculated_by_percent > 0){
                return trans('textos.precio_porcentual');
            }else{
                $price = $row->priceForCustomer? $row->priceForCustomer:$row->price;
                return $price.$currency->symbol;     
            }
        })
        ->addColumn('acciones', function($row){
            //Indica si es tabla de clientes o proveedores.
            $environment = 'providers';
            return view('admin.customer_products.partials.dt-actions', compact('row', 'environment'));    
        })
        ->rawColumns(['internal_ref', 'image'])
        ->make(true);
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
