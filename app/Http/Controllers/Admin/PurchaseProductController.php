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
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\PurchaseProduct;

class PurchaseProductController extends Controller{
    /**
     * 1. Listado de productos por empresa.
     * 1.1. Listado de productos para DataTable.
     * 2. Formulario nuevo producto.
     * 3. Guardar nuevo producto.
     * 4. Editar producto.
     * 5. Actualizar producto.
     * 6. Activar / desactivar producto.
     * 7. Eliminar producto.
     * 8. Productos para autocomplete.
     */
    
    private $option = 'catalogo';  
    private $module = 'products';
    private $languages;
    
    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:products');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);
        }
    } 

    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.productos_contables',
            'module' => $this->module,
            'slug' => 'purchase-products'
        ];

        $company =  Company::find(session('currentCompany'));

        return view('admin.purchase_products.index')->with(compact('header', 'company'));  
    }

    /**
     * 1.1. Listado de productos para DataTable.
     */
    public function getProductsDT(){
        $data = PurchaseProduct::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC');

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('iva', function($row){
            if($row->iva) return $row->iva.'%';
        })
        ->addColumn('account', function($row){
            if($row->accountingAccount) return $row->accountingAccount->account.' - '.$row->accountingAccount->name;
        })
        ->addColumn('acciones', function($row){
            return view('admin.purchase_products.partials.dt-actions', compact('row'));    
        })
        ->make(true);
    }

    /**
     * 2. Formulario nuevo producto.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.producto_nuevo',
            'module' => $this->module,
            'slug' => 'purchase-products'
        ];

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return view('admin.purchase_products.create')->with(compact('header', 'currency'));  
    }

    /**
     * 3. Guardar nuevo producto.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/purchase-products/create')
                ->withErrors($validator)
                ->withInput();
        }

        $product = new PurchaseProduct();
        $product->company_id = session('currentCompany');
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;
        $product->iva = $request->iva;
        $product->accounting_account_id = $request->accounting_account;
        $product->status = $request->status? 1:'0';
        $product->save();

        $msg = trans('textos.producto_creado_ok');
        return redirect()->route('purchase-products.edit', $product)->with(compact('msg'));
    }

    /**
     * 4. Editar producto.
     */
    public function edit(PurchaseProduct $product){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.producto_editar',
            'module' => $this->module,
            'slug' => 'purchase-products'
        ];

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Cuenta contable:
        $account = $product->accountingAccount;

        return view('admin.purchase_products.edit')->with(compact('header', 'product', 'currency', 'account'));  
    }

    /**
     * 5. Actualizar producto.
     */
    public function update(Request $request, PurchaseProduct $product){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/purchase-products/'.$product->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;
        $product->iva = $request->iva;
        $product->accounting_account_id = $request->accounting_account;
        $product->status = $request->status? 1:'0';
        $product->save();

        $msg = trans('textos.producto_updated');
        return redirect()->route('purchase-products.edit', $product)->with(compact('msg'));
    }

    /**
     * 6. Activar / desactivar producto.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $product = PurchaseProduct::find($id);
            $product->status = $status;
            $product->save();

            $resp = ($product)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar producto.
     */
    public function destroy(PurchaseProduct $product){
        $product->delete();

        $msg = trans('textos.producto_deleted');
        return redirect()->route('purchase-products.index')->with(compact('msg'));    
    }

    /**
     * 8. Productos para autocomplete.
     */
    public function searchProducts(Request $request){
        $search = $request->search;  
        
        $data = PurchaseProduct::select('id', 'name', 'iva')
        ->where('status', 1)
        ->where('company_id', session('currentCompany')) 
        ->where(function($q) use ($search){  
            $q->where('name', 'like', '%'.$search.'%') 
            ->orWhere('description', 'like', '%'.$search.'%');
        })
        ->orderBy('name', 'ASC')
        ->limit(15)
        ->get();

        return response()->json($data); 
    }
}
