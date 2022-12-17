<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $languages = $this->languages;

        return view('admin.accounting_options.index')->with(compact('header', 'options', 'languages'));       
    }

    /**
     * 2. Guardar opción.
     */
    public function store(Request $request){
        $field = $request->field;

        $data = AccountingOption::where('company_id', session('currentCompany'))->first();

        if(!$data){
            $data = new AccountingOption();
            $data->company_id = session('currentCompany');
            $data->$field = Carbon::now();

        }else{
            if($data->$field){
                $data->$field = NULL;
                
            }else{
                $data->$field = Carbon::now();
            }
        }
        $data->save();

        $msg = trans('textos.opcion_updated');
        return redirect()->route('accounting-options.index')->with(compact('msg'));
    }
}
