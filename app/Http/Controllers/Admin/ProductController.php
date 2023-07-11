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
use App\Models\Attribute;
use App\Models\BudgetFormItems;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CostCenters;
use App\Models\CustomerProducts;
use App\Models\CustomerProviders;
use App\Models\Product;
use App\Models\ProductAttributes;
use App\Models\ProductDocs;
use App\Models\ProductFavorites;
use App\Models\ProductPatterns;
use App\Models\ProductPrices;
use App\Models\ProductProviders;
use App\Models\ProductUnits;

class ProductController extends Controller{
    /**
     * 1. Listado de productos por empresa.
     * 1.1. Listado de productos para DataTable.
     * 2. Formulario nuevo producto.
     * 3. Guardar nuevo producto.
     * 4. Editar producto.
     * 5. Actualizar producto.
     * 6. Activar / desactivar producto.
     * 7. Eliminar producto.
     * 8. Mostrar producto.
     * 9. Productos para autocomplete.
     */  
    
    private $option = 'catalogo';  
    private $module = 'products';
    private $languages;  

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:products');
        $this->middleware('product_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'type' => 'required|in:p,s'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'iva' => 'nullable|numeric'
            ]);
        }
    } 

    /**
     * 1. Listado de productos por empresa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.productos',
            'module' => $this->module,
            'slug' => 'products'
        ];

        $view = 'list';

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $company =  Company::find(session('currentCompany'));

        return view('admin.products.index')->with(compact('header', 'view', 'currency', 'company'));    
    }

    /**
     * 1.1. Listado de productos para DataTable.
     */
    public function getProductsDT($company_id = false){
        $company = $company_id? $company_id:session('currentCompany'); 

        $products = Product::companyProducts($company, $company, false, false);

        //Permisos del usuario:
        $editar = Auth::user()->can('products.edit');
        $eliminar = Auth::user()->can('products.destroy');

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

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($products)->toJson();
    }

    /**
     * 2. Formulario nuevo producto.
     *
     * $company_id -> empresa tutelada por la empresa en session, se crea un producto del proveedor.
     * @return \Illuminate\Http\Response
     */
    public function create($company_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.producto_nuevo',
            'module' => $this->module,
            'slug' => 'products'
        ];

        //Moneda de la empresa tutelada:
        $currency = $company_id? CompanySettings::co_currency(false, $company_id):false;

        return view('admin.products.create')->with(compact('header', 'company_id', 'currency'));
    }

    /**
     * 3. Guardar nuevo producto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/products/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = $slug;
        $product->company_id = $request->tutored? $request->tutored:session('currentCompany');
        $product->user_id = Auth::user()->id;
        $product->type = $request->type;
        $product->on_sale = $request->on_sale? true:false;
        if($request->tutored){
            $product->iva = config('constants.IVA_DEFAULT_');
            $product->status = 1;
        }
        $product->save();

        //Si el producto pertenece a una empresa tutelada, el precio se guarda en customer_products:
        if($request->tutored && $request->price){
            $cc = new CustomerProducts();
            $cc->product_id = $product->id;
            $cc->provider_id = $request->tutored;
            $cc->customer_id = session('currentCompany');
            $cc->price = $request->price;
            $cc->save();
        }

        $msg = trans('textos.producto_creado_ok');
        
        //Redirección para empresas tuteladas:
        if($request->tutored){
            //Regreso a referencia de proveedor para cliente:
            if($request->return && $request->return == 'product-providers'){
                //Guardamos el producto como referencia de proveedor para la empresa en session:
                $pp = new ProductProviders();
                $pp->customer_product_id = $request->customer_product_id;
                $pp->provider_product_id = $product->id;
                $pp->provider_id = $request->tutored;
                $pp->provider_product = $request->name;
                $pp->save();

                return redirect()->route('product-providers.create', [$request->customer_product_id, $request->tutored])->with(compact('msg'));

            }else{
                //Obtenemos id de relación entre cliente y proveedor:
                $cp = CustomerProviders::select('id')
                ->where('customer_id', session('currentCompany'))
                ->where('provider_id', $request->tutored)
                ->first();
                return redirect()->route('provider-products.index', $cp)->with(compact('msg'));
            }

        }else{
            return redirect()->route('products.edit', $product)->with(compact('msg'));    
        }
    }

    /**
     * 4. Editar producto.
     *
     * @param  \App\product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.producto_editar',
            'module' => $this->module,
            'slug' => 'products'
        ];

        //$productions = ProductProduction::where('product_id', $product->id)
        //->first();
        $productions = false;

        //Patrones de referencia de producto:
        if(!$product->ref){
            $patterns = ProductPatterns::select('id', 'name')
                ->where('company_id', $product->company_id)
                ->where('status', 1)
                ->get();
        }else{
            $patterns = false;
        }

        //Tipos de datos para atributos:
        $data_types = helpers::dataTypes();

        //Obviamos los atributos ya vinculados:
        $attributes_in_product = ProductAttributes::where('product_id', $product->id)
        ->pluck('attribute_id')
        ->toArray();

        $attributes = Attribute::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->whereNotIn('id', $attributes_in_product)
        ->pluck('name', 'id')
        ->toArray();

        //Product serials:
        // $serials = ProductSerials::where('product_id', $product->id)
        // ->orderBy('id', 'ASC')
        // ->get();
        //$serials = false;

        //Histórico de precios:
        $prices = ProductPrices::where('product_id', $product->id)
        ->orderBy('id', 'DESC')
        ->get();

        //Centros de coste:
        $cost_centers = CostCenters::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //IVA:
        $iva = CompanySettings::co_iva();

        //Estados de producción:
        $production_status = helpers::productionProductStatus();

        $languages = $this->languages;

        //Módulos activos:
        $module_production = $this->settedModule('production');

        return view('admin.products.edit', compact('header', 'product', 'tab', 'productions', 'patterns', 'data_types', 'attributes', 'prices', 'cost_centers', 'currency', 'iva', 'production_status', 'languages', 'module_production'));
    }

    /**
     * 5. Actualizar producto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/products/'.$product->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;
        $stock_management = $request->stock_management? 1:0;
        $batch = $request->batch? 1:0;
        
        //Referencia de producto:
        $ref = false;
        if($request->product_pattern_id){
            $pattern = ProductPatterns::select('id', 'pattern')
            ->find($request->product_pattern_id);   

            if(!$pattern){
                return redirect('/');
                exit;
            }else{ 
                //Obtenemos número de productos con este patrón:
                $productsQty = Product::select('id')
                ->where('product_pattern_id', $pattern->id)
                ->get();

                //Manipulamos patrón para introducir número:
                $arr = unserialize($pattern->pattern);
                $num = '';
                foreach($arr as $idx => $p){
                    if($p[0] == '|' && $p[strlen($p) - 1] == '|'){
                        $x = trim($p,'|');

                        //Agregamos año al número de pedido.
                        if($x == 'year'){
                            $num .= date('Y');

                        //Agregamos numerador al número de pedido.
                        }else{
                            //contamos nº de dígitos:
                            $digitsQty = strlen($x);
                            //nuevo número de pedido:
                            $orderNum = $productsQty->count() + 1;
                            //dígitos del nuevo número de pedido:
                            $digitsOrderNum = strlen($orderNum);
                            //0 a la izquierda:
                            $num0 = $digitsQty - $digitsOrderNum;

                            for($k=0; $k<$num0; $k++){
                                $num .= '0';
                            }
                            $num .= $orderNum;
                        }
                    }else{
                        $num .= $p;
                    }
                }
                $ref = $num;
            }
        }

        //Guardar histórico de precios. La fecha de creación del registro indica hasta que fecha estuvo vigente el precio del producto hasta su actualización.
        if($product->price != $request->price && $product->price > 0){
            ProductPrices::create([
                'product_id' => $product->id,
                'price' => $product->price
            ]);
        }

        $arrTranslations = [];
        $arrTags = [];
        $arrDescription = [];
        $arrLongDescription = [];
        foreach($this->languages as $lang){
            $translations = 'translations_'.$lang[0];
            $tags = 'tags_'.$lang[0];
            $description = 'description_'.$lang[0];
            $long_description = 'long_description_'.$lang[0];

            $$tags = explode(';',$request->$tags);

            $arrTranslations[$lang[0]] = $request->$translations;
            $arrTags[$lang[0]] = $$tags;
            $arrDescription[$lang[0]] = $request->$description;
            $arrLongDescription[$lang[0]] = $request->$long_description;
        }
        $strTranslations = !empty($arrTranslations)? serialize($arrTranslations):NULL;
        $strTags = !empty($arrTags)? serialize($arrTags):NULL;
        $strDescription = !empty($arrDescription)? serialize($arrDescription):NULL;
        $strLongDescription = !empty($arrLongDescription)? serialize($arrLongDescription):NULL;

        $product->name = $request->name;
        $product->slug = $slug;
        $product->manual_ref = $request->manual_ref;
        if($ref){
            $product->ref = $ref;    
        }
        if($request->product_pattern_id){
            $product->product_pattern_id = $request->product_pattern_id;
        }
        $product->translations = $strTranslations;
        $product->tags = $strTags;
        $product->description = $strDescription;
        $product->long_description = $strLongDescription;
        $product->price = $request->price;
        $product->calculated_by_percent = $request->calculated_by_percent;
        $product->batch = $batch;
        $product->stock_management = $stock_management;
        $product->iva = $request->iva;
        $product->on_sale = $request->input('on_sale')? true:false;
        $product->cost_center_id = $request->cost_center;
        $product->production_status = $request->production_status;
        $product->status = $status;
        $product->save();

        //Se actualiza el nombre del producto en los formularios de presupuestos que incluyan este producto:
        BudgetFormItems::where('product_id', $product->id) 
        ->update(['name' => $product->name]);

        $msg = trans('textos.producto_updated');
        return redirect()->route('products.edit', $product->id)->with(compact('msg'));
    }

    /**
     * 6. Activar / desactivar producto.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $product = Product::find($id);
            $product->status = $status;
            $product->save();

            $resp = ($product)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar producto.
     */
    public function destroy(Product $product){
        //Eliminamos documentos asociados al producto:
        $docs = ProductDocs::select('id', 'doc')->where('product_id', $product->id)->get();

        if($docs->count()){
            foreach($docs as $doc){
                File::delete(public_path().'/img/products/'.$doc->doc);
                $doc->delete();
            }
        }

        $product->delete();

        $msg = trans('textos.producto_deleted');
        return redirect()->route('products.index')->with(compact('msg'));    
    }

    /**
     * 8. Mostrar producto.
     */
    public function show(Product $product){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.producto_ficha',
            'module' => $this->module,
            'slug' => 'products'
        ]; 

        //Si el producto no pertenece a la empresa en sesión, comprobamos si es producto de proveedor y el precio está personalizado.
        $customer_product = false;
        if($product->company_id != session('currentCompany')){
            $customer_product = CustomerProducts::select('price')
            ->where('product_id', $product->id)
            ->where('provider_id', $product->company_id)
            ->where('customer_id', session('currentCompany'))
            ->first();
        }

        //Documentos:
        $docs = ProductDocs::select('id', 'doc', 'type', 'featured')
        ->where('product_id', $product->id) 
        ->orderBy('featured', 'DESC')
        ->get();

        //Unidades de venta:
        $units = ProductUnits::select('units.name', 'units.symbol')
        ->join('units', 'product_units.unit_id', '=', 'units.id')
        ->where('product_units.product_id', $product->id)
        ->where('units.status', 1)
        ->get();

        //Favorito:
        $favorite = ProductFavorites::select('id')
        ->where('product_id', $product->id)
        ->where('user_id', Auth::user()->id)
        ->first();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //IVA:
        $iva = CompanySettings::co_iva();

        $languages = $this->languages;

        return view('admin.products.show', compact('header', 'product', 'customer_product', 'docs', 'units', 'favorite', 'currency', 'iva', 'languages'));  
    }

    /**
     * 9. Productos para autocomplete.
     */
    public function searchProducts(Request $request){
        $search = $request->search;  
        $type = $request->type? $request->type:false;
        $on_sale = $request->on_sale? $request->on_sale:false; 
        
        $data = Product::select('id', 'name', 'ref', 'manual_ref', 'price', 'iva')
        ->where('status', 1)
        ->where('company_id', session('currentCompany')) 
        ->where(function($q) use ($search){  
            $q->where('name', 'like', '%'.$search.'%') 
            ->orWhere('ref', 'like', '%'.$search.'%')   
            ->orWhere('manual_ref', 'like', '%'.$search.'%');
        })
        ->when($type, function($q) use($type){
            $q->where('type', $type);
        })
        ->when($on_sale, function($q) use($on_sale){
            $q->where('on_sale', $on_sale);
        })
        ->orderBy('name', 'ASC')
        ->limit(15)
        ->get();

        return response()->json($data); 
    }
}
