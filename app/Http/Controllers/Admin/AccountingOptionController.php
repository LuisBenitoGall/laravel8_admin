<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

//Models:
use App\Models\AccountingOption;

class AccountingOptionController extends Controller{
    /**
     * 1. Opciones contables.
     * 2. Guardar opción.
     */
    
    private $option = 'opciones_contables';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
    }

    /**
     * 1. Opciones contables.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.opciones',
            'module' => $this->module,
            'slug' => 'accounting-options'
        ];  

        $options = AccountingOption::where('company_id', session('currentCompany'))->first();

        $months = helpers::yearMonths();

        $sales = AccountingOption::select(DB::raw("CONCAT(year, '-', month) as fecha"))
        ->where('company_id', session('currentCompany'))
        ->where('type', 's')
        ->pluck('fecha')
        ->toArray();

        $purchases = AccountingOption::select(DB::raw("CONCAT(year, '-', month) as fecha"))
        ->where('company_id', session('currentCompany'))
        ->where('type', 'p')
        ->pluck('fecha')
        ->toArray();

        $languages = $this->languages;

        return view('admin.accounting_options.index')->with(compact('header', 'options', 'months', 'sales', 'purchases', 'languages'));       
    }

    /**
     * 2. Guardar opción.
     */
    public function store(Request $request, $year, $month, $type){
        if($request->ajax()){
            $r = AccountingOption::select('id')
            ->where('company_id', session('currentCompany'))
            ->where('type', $type)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

            if($r){
                $r->delete();
                $response = false;

            }else{
                $data = new AccountingOption();
                $data->company_id = session('currentCompany');
                $data->type = $type;
                $data->year = $year;
                $data->month = $month;
                $data->save();  
                $response = true;  
            }            

            return response()->json($response);
        }
    }
}
