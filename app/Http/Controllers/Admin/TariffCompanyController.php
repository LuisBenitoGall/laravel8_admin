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
use App\Models\TariffCompany;

class TariffCompanyController extends Controller{
    /**
     * 1. Clientes por tarifa.
     * 1.1. Listado de clientes por tarifa para DataTable.
     * 2. Vincular cliente a tarifa.
     * 3. Desvincular a cliente de tarifa.
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
                'tariff_id' => 'required|integer',
                'customer_id' => 'required|integer'
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
     * 1. Clientes por tarifa.
     */
    public function index(Tariff $tariff){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.tarifa_clientes',
            'module' => $this->module,
            'slug' => 'tariffs'
        ]; 

        return view('admin.tariff_companies.index')->with(compact('header', 'tariff'));      
    }

    /**
     * 1.1. Listado de clientes por tarifa para DataTable.
     */
    public function getTariffCompaniesDT($tariff_id){
        $data = TariffCompany::select('companies.name AS company', 'tariff_companies.*')
        ->join('companies', 'tariff_companies.company_to', '=', 'companies.id')
        ->where('tariff_companies.tariff_id', $tariff_id)
        ->where('tariff_companies.status', 1)
        ->orderBy('company', 'ASC');

        return DataTables::of($data)
        ->addColumn('company', function($row){
            return $row->company;
        })

        ->addColumn('acciones', function ($row){
            return view('admin.tariff_companies.partials.dt-actions', compact('row')); 
        })
        //->rawColumns([])
        ->make(true);
    }

    /**
     * 2. Vincular cliente a tarifa.
     *
     * Cada vez que se vincula un cliente a una tarifa, se desvinculan las anteriores del mismo proveedor que estuvieran activas.  Por tanto, un cliente sólo tiene una tarifa activa por proveedor.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $validator->getMessageBag()->add('accion', 'modal-add-customer');

            return redirect('/admin/tariff-companies/'.$request->tariff_id)
                ->withErrors($validator)
                ->withInput();
        }    

        //Comprobamos anteriores vinculaciones:
        TariffCompany::join('tariffs', 'tariff_companies.tariff_id', '=', 'tariffs.id')
        ->where('tariff_companies.company_to', $request->customer_id)
        ->where('tariffs.company_id', session('currentCompany'))
        ->update(['tariff_companies.status' => false]);

        $tc = new TariffCompany();
        $tc->tariff_id = $request->tariff_id;
        $tc->company_to = $request->customer_id;
        $tc->status = 1;
        $tc->created_by = Auth::user()->id;
        $tc->save();

        $msg = 'La tarifa se ha vinculado correctamente.';
        return redirect()->route('tariff-companies.index', $request->tariff_id)->with(compact('msg'));
    }

    /**
     * 3. Desvincular a cliente de tarifa.
     */
    public function destroy(TariffCompany $tariff){
        $tariff->delete();

        $msg = 'El cliente ha sido desvinculado correctamente.';
        return redirect()->route('tariff-companies.index', $tariff->tariff_id)->with(compact('msg'));
    }
}
