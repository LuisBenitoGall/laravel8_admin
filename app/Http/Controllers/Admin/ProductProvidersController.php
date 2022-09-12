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
use File;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Product;
use App\Models\ProductProviders;

class ProductProvidersController extends Controller{
    /**
     * 1. Listado de proveedores por producto.
     * 1.1. Lista de proveedores por producto para DataTable.
     * 2.1. Set proveedor.
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

        $data = ProductProviders::select('product_providers.*', 'companies.name', 'products.name AS product', 'products.price', 'products.ref')
        ->join('companies', 'provider_id', '=', 'companies.id')
        ->leftJoin('products', 'product_providers.provider_product_id', '=', 'products.id')
        ->where('product_providers.customer_product_id', $product_id)
        ->orderBy('companies.name', 'ASC');

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
            // Enlazar el precio personalizado para un cliente.
            return $row->price.$currency->symbol;
        })
        ->addColumn('acciones', function($row){
            return view('admin.product_providers.partials.dt-actions', compact('row'));    
        })
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

        $providers = false;
        //La empresa ha de tener habilitada la gestión de proveedores:
        if(session('companySettings') && session('companySettings')['providers_management']){
            $providers = CustomerProviders::select('companies.id', 'companies.name', 'companies.tradename')
            ->join('companies', 'customer_providers.provider_id', '=', 'companies.id')
            ->where('customer_providers.customer_id', session('currentCompany'))
            ->orderBy('companies.name', 'ASC')
            ->get();
        }

        //Moneda:
        $currency = $this->currency;

        return view('admin.product_providers.create')->with(compact('header', 'product', 'provider', 'providers', 'currency'));
    }

    /**
     * 2.1. Set proveedor.
     */
    public function setProvider(Request $request, Product $product){
        return redirect()->route('product-providers.create', [$product->id, $request->provider_id]);
    }
}
