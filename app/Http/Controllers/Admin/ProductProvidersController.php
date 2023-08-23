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
use File;

//Models:
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\CompanySettings;
use App\Models\CustomerProducts;
use App\Models\CustomerProviders;
use App\Models\Product;
use App\Models\ProductProviders;

class ProductProvidersController extends Controller{
    /**
     * 1. Listado de proveedores por producto.
     * 1.1. Lista de proveedores por producto para DataTable.
     * 2. Formulario correspondencia entre productos.
     * 2.1. Set proveedor.
     * 2.2. Catálogo de productos del proveedor para DataTable.
     * 3. Guardar correspondencia de producto de proveedor.
     * 3.1. Guardar correspondencia de producto de proveedor desde catálogo de proveedor.
     * 4. Obtener correspondencia de referencia de producto.
     * 5. Desvincular proveedor.
     */

    private $option = 'catalogo';  
    private $module = 'products';
    private $currency;
    private $languages;  

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:products');
        $this->middleware('product_get', ['only' => ['index', 'create', 'setProvider']]);
    }

    /**
     * 1. Listado de proveedores por producto.
     *
     * Acceden los clientes para listar los proveedores de su producto.
     */
    public function index(Product $product){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.producto_proveedores',
            'module' => $this->module,
            'slug' => 'products'
        ];

        return view('admin.product_providers.index')->with(compact('header', 'product'));
    }

    /**
     * 1.1. Lista de proveedores por producto para DataTable.
     */
    public function getProvidersDT($product_id){
        //Moneda:
        $currency = $this->currency;

        $data = ProductProviders::select('product_providers.*', 'companies.name', 'products.name AS product', 'products.price', 'products.ref',
            DB::raw('(SELECT price FROM customer_products WHERE product_id = product_providers.provider_product_id AND provider_id = product_providers.provider_id AND customer_id = '.session('currentCompany').') AS priceForCustomer')
        )
        ->join('companies', 'provider_id', '=', 'companies.id')
        ->leftJoin('products', 'product_providers.provider_product_id', '=', 'products.id')
        ->where('product_providers.customer_product_id', $product_id)
        ->orderBy('companies.name', 'ASC')
        ->groupBy('products.id');

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('product', function($row){
            if($row->product){
                return $row->product;    
            }else{
                return $row->provider_product;
            }
        })
        ->addColumn('ref', function($row){
            if($row->ref){
                return $row->ref;    
            }else{
                return $row->provider_ref;
            }
        })   
        ->addColumn('price', function($row) use($currency){
            $price = $row->priceForCustomer? $row->priceForCustomer:$row->price;

            return '<span style="display: none">'.$price.'</span>'.$price.$currency->symbol;
        })
        ->addColumn('acciones', function($row){
            $selector = false;
            return view('admin.product_providers.partials.dt-actions', compact('row', 'selector'));    
        })
        ->rawColumns(['price'])
        ->make(true);
    }

    /**
     * 2. Formulario correspondencia entre productos.
     */
    public function create(Product $product, $provider_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.proveedor_nuevo',
            'module' => $this->module,
            'slug' => 'products'
        ];

        $provider = $provider_id? Company::find($provider_id):false;

        //Cuenta de empresa del proveedor:
        $account = false;
        if($provider){
            $account = CompanyAccounts::select('id', 'guardian', 'account_id')
            ->where('company_id', $provider->id)
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->first();
        }

        $providers = false;
        //La empresa ha de tener habilitada la gestión de proveedores:
        if(session('companySettings') && session('companySettings')['providers_management']){
            $providers = CustomerProviders::select('companies.id', 'companies.name', 'companies.tradename')
            ->join('companies', 'customer_providers.provider_id', '=', 'companies.id')
            ->where('customer_providers.customer_id', session('currentCompany'))
            ->orderBy('companies.name', 'ASC')
            ->get();
        }

        //Productos fuera de catálogo:
        $products_out_catalogue = ProductProviders::where('provider_id', $provider_id)
        ->where('customer_product_id', $product->id)
        ->whereNull('provider_product_id')
        ->orderBy('provider_product', 'ASC')
        ->get();

        //Moneda:
        $currency = $this->currency;

        return view('admin.product_providers.create')->with(compact('header', 'product', 'provider', 'account', 'providers', 'products_out_catalogue', 'currency'));
    }

    /**
     * 2.1. Set proveedor.
     */
    public function setProvider(Request $request, Product $product){
        return redirect()->route('product-providers.create', [$product->id, $request->provider_id]);
    }

    /**
     * 2.2. Catálogo de productos del proveedor para DataTable.
     */
    public function getProvidersCorrespondencesDT($product_id, $provider_id){
        //Moneda:
        $currency = $this->currency;

        $data = Product::select('products.id', 'product_providers.id AS ppId', 'product_providers.provider_product_id', 'product_providers.provider_id', 'product_providers.provider_ref', 'product_providers.provider_product', 'products.name', 'products.ref', 'products.price', 'products.company_id',
            DB::raw('(SELECT doc FROM product_docs WHERE product_id = products.id AND featured = 1 LIMIT 1) AS Image'),
            DB::raw('(SELECT price FROM customer_products WHERE product_id = products.id AND provider_id = '.$provider_id.' AND customer_id = '.session('currentCompany').') AS priceForCustomer')
        )
        ->leftJoin('product_providers', 'products.id', '=', 'product_providers.provider_product_id')
        ->where('products.status', 1)
        ->where('products.on_sale', 1)
        ->where('products.company_id', $provider_id)
        ->whereNull('product_providers.deleted_at')
        ->orderBy('product_providers.provider_product_id', 'DESC')
        ->orderBy('products.name', 'ASC')
        ->groupBy('products.id');

        return DataTables::of($data)
        ->editColumn('name', function($row){
            return $row->name? $row->name:$row->provider_product;
        })
        ->editColumn('ref', function($row){
            return $row->ref? $row->ref:$row->provider_ref;
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="/img/products/'.$row->Image.'" width="50"><span style="display: none">1</span>'; 
            }else{
                return '<i class="fa fa-gift icono"></i><span style="display: none">0</span>';      
            }    
        })
        ->addColumn('price', function($row) use($provider_id, $currency){
            $price = $row->priceForCustomer? $row->priceForCustomer:$row->price;

            return '<span style="display: none">'.$price.'</span>'.$price.$currency->symbol;
        })
        //25/09/2022: Revisar el filtro de price, no funciona:
        ->filterColumn('price', function($query, $keyword, $row){
            $query->where('products.price', $keyword)
            ->whereHas('customer_products', function($q) use($keyword, $row){
                $q->where('product_id', $row->id)
                ->where('provider_id', $row->company_id)
                ->where('customer_id', session('currentCompany'))
                ->where('price', 'LIKE', "%$keyword%");
            });


            //$sql = "products.price = ? or customer_products.priceForCustomer = ?";
            //$query->whereRaw($sql, [$keyword]);
        })
        ->addColumn('acciones', function($row){
            $selector = true;
            return view('admin.product_providers.partials.dt-actions', compact('row', 'selector'));    
        })
        ->rawColumns(['image', 'price'])
        ->make(true);
    }

    /**
     * 3. Guardar correspondencia de producto de proveedor.
     */
    public function store(Request $request){
        if($request->ajax()){  
            $exist = $this->existRefProviderCustomer($request->customer_product_id, $request->provider_product_id, $request->provider_id);

            if(!$exist){
                $provider_product = Product::select('name', 'ref')->find($request->provider_product_id);

                $row = new ProductProviders();
                $row->customer_product_id = $request->customer_product_id;
                $row->provider_product_id = $request->provider_product_id;
                $row->provider_id = $request->provider_id;
                $row->provider_ref = $provider_product? $provider_product->name:NULL;
                $row->provider_product = $provider_product? $provider_product->ref:NULL;
                $row->save();

            }else{
                ProductProviders::where('customer_product_id', $request->customer_product_id)
                ->where('provider_product_id', $request->provider_product_id)  
                ->where('provider_id', $request->provider_id) 
                ->delete();
            }   

            return response()->json(true);
        } 
    }

    /**
     * 3.1. Guardar correspondencia de producto de proveedor desde catálogo de proveedor.
     */
    public function storeFromProvider(Request $request){
        //Proveedor del producto:
        $provider = Product::select('company_id')->find($request->provider_product_id);

        $exist = $this->existRefProviderCustomer($request->customer_product_id, $request->provider_product_id, $provider->company_id);

        //Relación cliente proveedor:
        $cp = CustomerProviders::select('id')
        ->where('customer_id', session('currentCompany'))
        ->where('provider_id', $provider->company_id)
        ->first();

        if(!$exist){
            $row = new ProductProviders();
            $row->customer_product_id = $request->customer_product_id;
            $row->provider_product_id = $request->provider_product_id;
            $row->provider_id = $provider->company_id;
            $row->save();

            $msg = trans('textos.correspondencia_ref_saved');
            return redirect()->route('provider-products.index', $cp->id)->with(compact('msg'));

        }else{
            $alert = trans('textos.correspondencia_ref_alert');
            return redirect()->route('provider-products.index', $cp->id)->with(compact('alert'));
        }
    }

    /**
     * 3.2. Verificando existe relación de referencia entre cliente y proveedor.
     */
    public function existRefProviderCustomer($customer_product_id, $provider_product_id, $provider_id){
        return ProductProviders::where('customer_product_id', $customer_product_id)
            ->where('provider_product_id', $provider_product_id)  
            ->where('provider_id', $provider_id) 
            ->first();
    }

    /**
     * 4. Obtener correspondencia de referencia de producto.
     */
    public function show(Request $request, $product_id){
        if($request->ajax()){
            $data = Product::select('products.id', 'products.name')
            ->join('product_providers', 'products.id', '=', 'product_providers.customer_product_id')
            ->where('product_providers.provider_product_id', $product_id)
            ->where('products.company_id', session('currentCompany'))
            ->first();

            return response()->json($data);
        }    
    }

    /**
     * 5. Desvincular proveedor.
     */
    public function destroy(ProductProviders $provider){
        $product_id = $provider->customer_product_id;

        $provider->delete();

        $msg = trans('textos.proveedor_desvinculado');
        return redirect()->route('product-providers.index', $product_id)->with(compact('msg'));  
    }
}
