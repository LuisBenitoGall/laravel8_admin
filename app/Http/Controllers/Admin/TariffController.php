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
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Tariff;
//use App\Models\TariffProductsExcluded;

class TariffController extends Controller{
    /**
     * 1. Listado de tarifas por empresa.
     * 1.1. Listado de tarifas para DataTable.
     * 2. Formulario nueva tarifa.
     * 3. Guardar nueva tarifa.
     * 4. Editar tarifa.
     * 5. Actualizar tarifa.
     * 6. Eliminar tarifa.
     * 7. Activar / desactivar tarifa.
     * 8. Marcar tarifa principal.
     * 8.1. Eliminar tarifa principal.
     * 9. Replicar productos premium para otras tarifas.
     */
    
    private $option = 'tarifas';
    private $module = 'products';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:products');
        //$this->middleware('product_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if (isset($data['submit_save'])) {
            return Validator::make($data, [
                'name' => 'required|string|max:100',
                'discount' => 'required|numeric'
            ]);

        //Validación update:
        } elseif (isset($data['submit_update'])) {
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'iva' => 'nullable|numeric'
            ]);
        }
    }
    
    /**
     * 1. Listado de tarifas por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.tarifas',
            'module' => $this->module,
            'slug' => 'tariffs'
        ]; 

        //Moneda:
        $currency = CompanySettings::co_currency(0); 

        $company = Company::find(session('currentCompany'));

        return view('admin.tariffs.index')->with(compact('header', 'currency', 'company'));  
    }

    /**
     * 1.1. Listado de tarifas para DataTable.
     */
    public function getTariffsDT(){
        $data = Tariff::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC');

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('discount', function($row){
            return $row->discount.'%';
        })
        ->addColumn('description', function($row){
            return $row->description;
        })
        ->addColumn('created_at', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('acciones', function ($row){
            return view('admin.tariffs.partials.dt-actions', compact('row')); 
        })
        //->rawColumns([])
        ->make(true);
    }

    /**
     * 2. Formulario nueva tarifa.
     */
    public function create(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.tarifa_nueva',
            'module' => $this->module,
            'slug' => 'tariffs'
        ]; 

        //Moneda:
        $currency = CompanySettings::co_currency(0); 

        return view('admin.tariffs.create')->with(compact('header', 'currency'));      
    }

    /**
     * 3. Guardar nueva tarifa.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/tariffs/create')
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->name);  

        $t = new Tariff();
        $t->name = $request->name;
        $t->slug = $slug;
        $t->description = $request->description;
        $t->company_id = session('currentCompany');  
        $t->discount = $request->discount;
        $t->created_by = Auth::user()->id;
        $t->status = $request->status? 1:'0';
        $t->save();

        $msg = 'La tarifa se ha guardado correctamente.';
        return redirect()->route('tariffs.edit', $t)->with(compact('msg'));
    }

    /**
     * 4. Editar tarifa.
     */
    public function edit(Tariff $tariff, $tab = false){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.tarifa_edit',
            'module' => $this->module,
            'slug' => 'tariffs'
        ]; 

        //Moneda:
        $currency = CompanySettings::co_currency(0); 

        //Productos excluidos de la tarifa:
        // $products_excluded = TariffProductsExcluded::select('products.id', 'products.name', 'products.ref', 'products.manual_ref', 'tariff_products_excluded.id AS pe_id', 'tariff_products_excluded.discount')
        // ->join('products', 'tariff_products_excluded.product_id', '=', 'products.id')
        // ->where('tariff_products_excluded.tariff_id', $tariff->id)
        // ->orderBy('products.name')
        // ->get();
        $products_excluded = [];

        //Otras tarifas:
        $other_tariffs = Tariff::select('id', 'name')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->where('id', '!=', $tariff->id)
        ->pluck('name', 'id')
        ->toArray();

        $languages = $this->languages;

        return view('admin.tariffs.edit')->with(compact('header', 'tariff', 'tab', 'currency', 'products_excluded', 'other_tariffs', 'languages'));
    }

    /**
     * 5. Actualizar tarifa.
     */
    public function update(Request $request, Tariff $tariff){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/tariffs/'.$tariff->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->name);  

        $tariff->name = $request->name;
        $tariff->slug = $slug;
        $tariff->description = $request->description;
        $tariff->discount = $request->discount;
        $tariff->status = $request->status? 1:'0';
        $tariff->save();

        $msg = 'La tarifa se ha actualizado correctamente.';
        return redirect()->route('tariffs.edit', $tariff)->with(compact('msg'));
    }

    /**
     * 6. Eliminar tarifa.
     */
    public function destroy(Tariff $tariff){
        $tariff->delete();

        $msg = trans('textos.tarifa_deleted');
        return redirect()->route('tariffs.index')->with(compact('msg'));
    }

    /**
     * 7. Activar / desactivar tarifa.
     */
    public function status(Request $request, $id, $status){
        if ($request->ajax()) {
            $tariff = Tariff::find($id);
            $tariff->status = $status;
            $tariff->save();

            $resp = ($tariff) ? true : false;
            return response()->json($resp);
        }
    }

    /**
     * 8. Marcar tarifa principal.
     */
    public function featured(Tariff $tariff){
        Tariff::where('company_id', $tariff->company_id)
          ->update(['featured' => 0]);

        $tariff->featured = 1;
        $tariff->save();

        $msg = trans('textos.tarifa_updated');
        return redirect()->route('tariffs.index')->with(compact('msg'));
    }

    /**
     * 8.1. Eliminar tarifa principal.
     */
    public function removeFeatured(Tariff $tariff){
        $tariff->featured = 0;
        $tariff->save();

        $msg = trans('textos.tarifa_sin_principal');
        return redirect()->route('tariffs.index')->with(compact('msg'));
    }

    /**
     * 9. Replicar productos premium para otras tarifas.
     */
    public function replyProducts(Request $request){
        //Productos de la tarifa original:
        $products = TariffProductsExcluded::select('id', 'product_id', 'discount')
        ->where('tariff_id', $request->tariff_id)
        ->get();

        foreach($request->tariffs as $t){
            //Comprobamos si el producto existe para cada tarifa y evitar duplicados:
            foreach($products as $p){
                $tariffProductExcluded = TariffProductsExcluded::firstOrCreate([
                    'tariff_id' => $t,
                    'product_id' => $p->product_id
                ],[
                    'discount' => $p->discount
                ]);
            }
        }

        $msg = trans('textos.productos_replicados');
        return redirect()->route('tariffs.edit', [$request->tariff_id, 'products'])->with(compact('msg'));
    }
}
