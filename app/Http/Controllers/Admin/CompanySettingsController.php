<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Currency;

class CompanySettingsController extends Controller{
    /**
     * 1. Página de configuración de empresa.
     * 2. Actualizar datos de configuración.
     */

    private $option = 'empresas';  
    private $module = 'companies';

    //0. Validator:
    protected function validator(array $data){
        //Validación update:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                'iva' => 'nullable|numeric|max:99'
            ],[
                'iva.numeric' => trans('textos.iva_valido_aviso'),
                'iva.max' => trans('textos.iva_valido_aviso')
            ]);
        }
    } 

    /**
     * 1. Página de configuración de empresa.
     */  
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.empresa_configuracion',
            'module' => $this->module,
            'slug' => 'company-settings'
        ];

        $company = Company::find(session('currentCompany'));

        if(!$company){
            return redirect()->route('logout');    
        }

        //Comprobamos si existe registro de configuración, siempre de la empresa en sesión:
        $company_settings = CompanySettings::where('company_id', session('currentCompany'))->first();
        
        if(!$company_settings){
            $company_settings = CompanySettings::create([
                'company_id' => session('currentCompany'),
                'iva' => config('constants.IVA_DEFAULT_')
            ]); 
        }

        $currencies_obj = Currency::where('status', 1)->get();
        //Pasamos a array:
        $currencies = [];
        foreach($currencies_obj as $co){
            $currencies[$co->id] = $co->name;
        }

        //Información pública:
        $public_items = Helpers::publicInfo();

        return view('admin.company_settings.index')->with(compact('header', 'company', 'company_settings', 'currencies', 'public_items'));
    }   

    /**
     * 2. Actualizar datos de configuración.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        if(!session('currentCompany')){
            return redirect('/');
            exit;
        }   

        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/company-settings')
                ->withErrors($validator)
                ->withInput();
        }

        $customers_management = $request->input('customers_management')? 1:0;
        $providers_management = $request->input('providers_management')? 1:0;
        $validate_nif = $request->input('validate_nif')? 1:0;

        $ip = $request->ip? serialize($request->ip):NULL;
        $emails = $request->emails? serialize($request->emails):NULL;

        //Información pública:
        $public_info_arr = [];
        if($request->public_info){
            foreach($request->public_info as $pi){
                array_push($public_info_arr, $pi);
            }
        }
        $public_info = count($public_info_arr)? serialize($public_info_arr):NULL;

        $cs = CompanySettings::find($request->input('id'));
        $cs->currency_id = $request->input('currency');
        $cs->customers_management = $customers_management;
        $cs->providers_management = $providers_management;
        $cs->validate_nif = $validate_nif;
        $cs->primary_color = $request->primary_color;
        $cs->secondary_color = $request->secondary_color;
        $cs->base_color_budgets = $request->base_color_budgets;
        $cs->base_color_orders = $request->base_color_orders;
        $cs->base_color_invoices = $request->base_color_invoices;
        $cs->iva = $request->iva;
        $cs->ip = $ip;
        $cs->emails = $emails;
        $cs->public_info = $public_info;
        $cs->accounting_account_digits = $request->accounting_account_digits;
        $cs->save();

        session(['companySettings' => $cs]);

        $msg = trans('textos.configuracion_updated');
        return redirect()->route('company-settings.index')->with(compact('msg'));
    }    
}
