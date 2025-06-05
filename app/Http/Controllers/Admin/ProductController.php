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
use Carbon\Carbon;

//Models:
use App\Models\Attribute;
use App\Models\BudgetFormItems;
use App\Models\BudgetForms;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CostCenters;
use App\Models\CustomerProducts;
use App\Models\CustomerProviders;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAttributes;
use App\Models\ProductDocs;
use App\Models\ProductFavorites;
use App\Models\ProductPatterns;
use App\Models\ProductPrices;
use App\Models\ProductProviders;
use App\Models\ProductUnits;
use App\Models\StockMovements;
use App\Models\Store;
use App\Models\UserCompanies;

class ProductController extends Controller{
    /**
     * 1. Listado de productos por empresa.
     * 1.1. Listado de productos para DataTable.
     * 2. Formulario nuevo producto.
     * 2.1. Formulario variante de producto.
     * 3. Guardar nuevo producto.
     * 4. Editar producto.
     * 5. Actualizar producto.
     * 6. Activar / desactivar producto.
     * 7. Eliminar producto.
     * 8. Mostrar producto.
     * 9. Productos para autocomplete.
     * 10. Listado de ventas por producto para DataTable.
     * 11. Listado de compras por producto para DataTable.
     * 12. Pedidos de compra del producto.
     * 13. Migración de catálogo y formulario de presupuestos entre empresas.
     * 14. Variantes de producto.
     * 14.1. Variantes de producto para DataTable.
     */  
    
    private $option = 'catalogo';  
    private $module = 'products';
    private $languages;  

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:products');
        $this->middleware('product_get', ['only' => ['edit', 'update', 'destroy', 'variants']]);
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

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $data = Product::companyProducts($company, $company, false, false, false, true);

        return DataTables::of($data)
        ->addColumn('product', function($row){
            //Enviamos el nombre del producto ya traducido:
            $trans = $row->translations? unserialize($row->translations):false;
            if($trans){
                $product_name = (isset($trans[session()->get('locale')]) && $trans[session()->get('locale')] != '' && $trans != '')? $trans[session()->get('locale')]:$row->name;
                return $product_name;
            }else{
                return $row->name;
            }
        })
        ->addColumn('ref', function($row){
            if($row->ref){
                return $row->ref;
            }else{
                return '';
            }
        })
        ->addColumn('description', function($row){
            $arr = $row->description? unserialize($row->description):false;
            if($arr){
                $description = (isset($arr[session()->get('locale')]) && $arr[session()->get('locale')] != '' && $arr != '')? $arr[session()->get('locale')]:'';
                return ucfirst($description); 
            }else{
                return '';
            }
        })
        ->addColumn('price', function($row) use($currency){
            if($row->price){
                return $row->price.$currency->symbol;
            }else{
                return '';
            }
        })  
        ->addColumn('cost_center', function($row){
            if($row->cost_center){
                return $row->cost_center;
            }else{
                return '';
            }
        })
        ->addColumn('image', function($row){
            if($row->Image){
                return '<img src="/img/products/'.$row->Image.'" width="50"><span style="display: none">1</span>';  
            }else{
                return '<i class="fa fa-gift icono"></i><span style="display: none">0</span>';
            }
        })
        ->addColumn('on_sale', function($row){
            if($row->on_sale){
                return '<i class="fa fa-check text-success"></i><span style="display: none">1</span>';
            }else{
                return '<i class="fa fa-ban text-danger"></i><span style="display: none">0</span>';    
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.products.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['image', 'on_sale'])
        ->make(true);
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

        $is_variant = false;

        //Moneda de la empresa tutelada:
        $currency = $company_id? CompanySettings::co_currency(false, $company_id):false;

        return view('admin.products.create')->with(compact('header', 'is_variant', 'company_id', 'currency'));
    }

    /**
     * 2.1. Formulario variante de producto.
     */
    public function createVariant(Product $product_ref){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.variante_nueva',
            'module' => $this->module,
            'slug' => 'products'
        ];

        $is_variant = true;

        $company_id = false;

        //Moneda de la empresa tutelada:
        $currency = $company_id? CompanySettings::co_currency(false, $company_id):false;

        return view('admin.products.create')->with(compact('header', 'product_ref', 'is_variant', 'company_id', 'currency'));
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

        //Slug. Si se trata de un producto tutelado, no guarda nombre ni slug.
        $slug = !$request->tutored? Str::slug($request->name):NULL;

        $product = new Product();
        if($request->variant_of > 0){
            $product->variant_of = $request->variant_of;
        }
        $product->name = !$request->tutored? $request->name:NULL;
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
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        //Atributos del producto de referencia:
        $ref_attributes = false;
        $variant_attributes = false;
        if($product->variant_of){
            $ref_attributes = Attribute::select('attributes.id', 'attributes.name', 'attributes.datatype', 'attributes.values', 'product_attributes.value')
            ->join('product_attributes', 'attributes.id', '=', 'product_attributes.attribute_id')
            ->where('product_attributes.product_id', $product->variant_of)
            ->where('attributes.status', 1)
            ->orderBy('attributes.name', 'ASC')
            ->get();

            $variant_attributes = ProductAttributes::select('attribute_id', 'value', 'value_in_description')
            ->where('product_id', $product->id)
            ->get();
        }

        //Product serials:
        // $serials = ProductSerials::where('product_id', $product->id)
        // ->orderBy('id', 'ASC')
        // ->get();
        //$serials = false;
        
        //Empresa:
        $company = Company::find(session('currentCompany'));

        //Histórico de precios:
        $prices = ProductPrices::where('product_id', $product->id)
        ->orderBy('id', 'DESC')
        ->get();

        //Centros de coste:
        $cost_centers = CostCenters::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Empleados:
        $employees = UserCompanies::select(
            DB::raw("CONCAT(users.name,' ',users.surname) AS user"), 'users.id')
        ->join('users', 'user_companies.user_id', '=', 'users.id')
        ->where('users.status', 1)
        ->where('user_companies.company_id', session('currentCompany'))
        ->orderBy('users.name')
        ->pluck('user', 'users.id')
        ->toArray();

        //Movimiento por defecto para asignación de material a usuario.
        $movement = StockMovements::select('id')
        ->where('acronym', 'ci')
        ->where('sign', 'x')
        ->first();

        //Almacenes de consumo interno.
        $stores = Store::where('company_id', session('currentCompany'))
        ->where('type', 'c')
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

        return view('admin.products.edit', compact('header', 'product', 'tab', 'productions', 'patterns', 'data_types', 'attributes', 'ref_attributes', 'variant_attributes', 'company', 'prices', 'cost_centers', 'employees', 'movement', 'stores', 'currency', 'iva', 'production_status', 'languages', 'module_production'));
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
        $product->billable_percentage = $request->billable_percentage;
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

    /**
     * 10. Listado de ventas por producto para DataTable.
     */
    public function getSalesDT($product_id){
        $data = Item::select('orders.id', 'orders.order_num', 'orders.order_old_num', 'orders.delivery_date', 'orders.created_at', 'companies.name', 'companies.tradename', 'items.price', 'items.qty', 'items.iva', 'items.discount', 'items.irpf', 'items.days', 'items.days_continue', 'items.days_continue_discount')
        ->join('orders', 'items.order_id', '=', 'orders.id')
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->where('items.product_id', $product_id)
        ->where('orders.provider_id', session('currentCompany'))
        ->orderBy('orders.delivery_date', 'DESC');

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('date', function($row){
            if($row->delivery_date){
                return Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]);   
            }else{
                return $row->created_at->format($this->languages[session()->get('locale')][4]);;
            }
        })
        ->addColumn('customer', function($row){
            return $row->name;
        })
        ->addColumn('order', function($row){
            $order_num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
            if(Auth::user()->can('products.edit')){
                return '<a target="_blank" href="/admin/orders/'.$row->id.'/edit">'.$order_num.'</a>';
            }else{
                return $order_num;
            }
        })
        ->addColumn('qty', function($row){
            return $row->qty;
        })
        ->addColumn('price', function($row) use($currency){
            return $row->price.$currency->symbol;
        })
        ->addColumn('discounts', function($row){
            if($row->discount > 0){
                return $row->discount.'%';
            }else{
                return '';
            }            
        })
        ->addColumn('iva', function($row){
            return $row->iva.'%';
        })
        ->addColumn('amount', function($row) use($currency){
            $amount = helpers::itemAmount($row, true);
            return $amount['amount_without_discount'].$currency->symbol;
        })
        ->addColumn('acciones', function($row){
            return view('admin.products.partials.dt-actions-sales', compact('row'));    
        })
        ->rawColumns(['order'])
        ->make(true);
    }
    
    /**
     * 11. Listado de compras por producto para DataTable.
     */
    public function getPurchasesDT($product_id){
        $data = Item::select('orders.id', 'orders.order_num', 'orders.order_old_num', 'orders.delivery_date', 'orders.created_at', 'companies.name', 'companies.tradename', 'items.price', 'items.qty', 'items.iva', 'items.discount', 'items.irpf', 'items.days', 'items.days_continue', 'items.days_continue_discount')
        ->join('orders', 'items.order_id', '=', 'orders.id')
        ->join('companies', 'orders.provider_id', '=', 'companies.id')
        ->join('product_providers', 'items.product_id', '=', 'product_providers.provider_product_id')
        ->where('product_providers.customer_product_id', $product_id)
        ->where('orders.customer_id', session('currentCompany'))
        ->orderBy('orders.delivery_date', 'DESC');

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('date', function($row){
            if($row->delivery_date){
                return Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]);   
            }else{
                return $row->created_at->format($this->languages[session()->get('locale')][4]);;
            }
        })
        ->addColumn('provider', function($row){
            return $row->name;
        })
        ->addColumn('order', function($row){
            $order_num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
            if(Auth::user()->can('products.edit')){
                return '<a target="_blank" href="/admin/orders/'.$row->id.'/edit">'.$order_num.'</a>';
            }else{
                return $order_num;
            }
        })
        ->addColumn('qty', function($row){
            return $row->qty;
        })
        ->addColumn('price', function($row) use($currency){
            return $row->price.$currency->symbol;
        })
        ->addColumn('discounts', function($row){
            if($row->discount > 0){
                return $row->discount.'%';
            }else{
                return '';
            }            
        })
        ->addColumn('iva', function($row){
            return $row->iva.'%';
        })
        ->addColumn('amount', function($row) use($currency){
            $amount = helpers::itemAmount($row, true);
            return $amount['amount_without_discount'].$currency->symbol;
        })
        ->addColumn('acciones', function($row){
            return view('admin.products.partials.dt-actions-sales', compact('row'));    
        })
        ->rawColumns(['order'])
        ->make(true);
    }

    /**
     * 12. Pedidos de compra del producto.
     */
    public function purchaseOrders(Request $request, $product_id){
        if($request->ajax()){ 
            $data = Order::select('orders.id', 'orders.name', 'orders.order_num', 'orders.order_old_num', 'companies.name AS company')
            ->join('items', 'orders.id', '=', 'items.order_id')
            ->join('product_providers', 'items.product_id', '=', 'product_providers.provider_product_id')
            ->join('companies', 'orders.provider_id', '=', 'companies.id')
            ->where('product_providers.customer_product_id', $product_id)
            ->where('orders.customer_id', session('currentCompany'))
            ->whereIn('orders.order_status', [1,10,20,70])
            ->orderBy('orders.created_at', 'ASC')
            ->get();

            return response()->json($data);
        }    
    }

    /**
     * 13. Migración de catálogo y formulario de presupuestos entre empresas.
     */
    public function migrateCatalogue($company_from, $company_to){
        $products = Product::select('products.*', 'budget_form_items.id AS bfi_id')
        ->leftJoin('budget_form_items', 'products.id', '=', 'budget_form_items.product_id')
        ->where('products.company_id', $company_from)
        ->where('products.on_sale', 1)
        ->whereNull('products.deleted_at')
        ->groupBy('products.id')
        ->get();

        //Nuevo formulario de presupuestos:
        $budget_form_to = new BudgetForms();
        $budget_form_to->name = 'Plantilla presupuestos MAD';
        $budget_form_to->slug = 'plantilla-presupuestos-mad';
        $budget_form_to->company_id = $company_to;
        $budget_form_to->status = 1;
        $budget_form_to->save();

        //Eliminamos los productos de la empresa de destino:
        Product::where('company_id', $company_to)
        ->where('on_sale', 1)
        ->delete();

        foreach($products as $p){
            $cost_center_from = $p->cost_center_id? CostCenters::select('slug')->find($p->cost_center_id):false;

            if($cost_center_from){
                $cost_center_to = CostCenters::select('id')
                ->where('slug', $cost_center_from->slug)
                ->where('company_id', $company_to)
                ->first();
            }else{
                $cost_center_to = false;    
            }

            $pd = new Product();
            $pd->name = $p->name;
            $pd->slug = $p->slug;
            $pd->translations = $p->translations;
            $pd->tags = $p->tags;
            $pd->description = $p->description;
            $pd->long_description = $p->long_description;
            $pd->company_id = $company_to;
            $pd->user_id = Auth::user()->id;
            $pd->type = $p->type;
            $pd->price = $p->price;
            $pd->calculated_by_percent = $p->calculated_by_percent;
            $pd->iva = $p->iva;
            $pd->on_sale = 1;
            $pd->cost_center_id = $cost_center_to? $cost_center_to->id:NULL;
            $pd->production_status = $p->production_status;
            $pd->status = $p->status;
            $pd->save();

            //Si el producto original estaba en formulario, se incluye en el nuevo formulario:
            if($p->bfi_id){
                $bfi = new BudgetFormItems();
                $bfi->form_id = $budget_form_to->id;
                $bfi->product_id = $pd->id;
                $bfi->name = $pd->name;
                $bfi->save();
            }
        }
    }

    /**
     * 14. Variantes de producto.
     */
    public function variants(Product $product){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.producto_variantes',
            'module' => $this->module,
            'slug' => 'products'
        ];

        return view('admin.products.variants', compact('header', 'product'));
    }

    /**
     * 14.1. Variantes de producto para DataTable.
     */
    public function getVariantsDT($product_id){
        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $data = Product::where('variant_of', $product_id)
        ->orderBy('name', 'ASC');

        return DataTables::of($data)
        ->addColumn('product', function($row){
            //Enviamos el nombre del producto ya traducido:
            $trans = $row->translations? unserialize($row->translations):false;
            //Concatenar atributos en las variantes:
            $attributes = $row->attributeValuesAsString(' ');

            if($trans){
                $product_name = (isset($trans[session()->get('locale')]) && $trans[session()->get('locale')] != '' && $trans != '')? $trans[session()->get('locale')]:$row->name;
                return $product_name.' <i>'.$attributes.'</i>';
            }else{
                return $row->name.' <i>'.$attributes.'</i>';
            }
        })
        ->addColumn('ref', function($row){
            if($row->ref){
                return $row->ref;
            }elseif($row->manual_ref){
                return $row->manual_ref;
            }else{
                return '';
            }
        })
        ->addColumn('attributes', function($row){
            return $row->attributeValuesAsString();   
        })
        ->addColumn('price', function($row) use($currency){
            if($row->price){
                return $row->price.$currency->symbol;
            }else{
                return '';
            }
        })  
        ->addColumn('image', function($row){
            $img = $row->featuredImage(); // Llama al método featuredImage

            if($img){ // Verifica si existe la imagen destacada
                return '<img src="/img/products/'.$img->doc.'" width="50"><span style="display: none">1</span>';  
            }else{
                return '<i class="fa fa-gift icono"></i><span style="display: none">0</span>'; // Valor por defecto
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.products.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['product', 'image'])
        ->make(true);
    }
}

