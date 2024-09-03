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

//Models:
use App\Models\AccountingAccount;
use App\Models\Company;
use App\Models\CustomerProviders;
use App\Models\TransportAgency;

class AgencyController extends Controller{
    /**
     * 1. Listado de agencias de transporte por cliente.
     * 2. Vincular agencia.
     * 3. Desvincular agencia.
     */
    
    private $option = 'transporte_agencias';
    private $module = 'logistics';
    private $languages;

    public function __construct(){
        $this->languages = helpers::languages();

        $this->middleware('module_setted:logistics');
    }


    /**
     * 1. Listado de agencias de transporte por cliente.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.transporte_agencias'),
            'module' => $this->module,
            'slug' => 'agencies'
        ];

        //Agencias de transporte:
        $transport_agencies = TransportAgency::select('companies.*', 'transport_agencies.id AS ta_id')
        ->join('companies', 'transport_agencies.company_id', '=', 'companies.id')
        ->orderBy('companies.name')
        ->get();

        //Agencias de la empresa en sesión:
        $my_agencies = CustomerProviders::select('customer_providers.provider_id')
        ->join('transport_agencies', 'customer_providers.provider_id', '=', 'transport_agencies.company_id')
        ->where('customer_providers.customer_id', session('currentCompany'))
        ->pluck('provider_id')
        ->toArray();

        $languages = $this->languages;

        return view('admin.agencies.index')->with(compact('header', 'transport_agencies', 'my_agencies', 'languages'));    
    }

    /**
     * 2. Vincular agencia.
     */
    public function store($provider_id){
        //Si existía previamente, se reactiva.
        $cc = CustomerProviders::where('provider_id', $provider_id)
        ->where('customer_id', session('currentCompany')) 
        ->first();

        if($cc){
            $cc->delete_at = NULL;
            $cc->save();

        }else{
            $cc = new CustomerProviders();
            $cc->customer_id = session('currentCompany');
            $cc->provider_id = $provider_id;
            $cc->status = 1;
            $cc->save();

            //Creando cuenta contable:
            $accounting_account_data = [
                'account_to' => 'provider',
                'company_target' => $provider_id,
                'mode' => 2,
                'with_iva' => 1
            ];
            AccountingAccount::setAccountingAccount($accounting_account_data);
        }

        $msg = trans('textos.agencia_vinculada_ok');
        return redirect()->route('providers.edit', $provider_id)->with(compact('msg'));
    }

    /**
     * 3. Desvincular agencia.
     */
    public function destroy($provider_id){
        $cc = CustomerProviders::where('provider_id', $provider_id)
        ->where('customer_id', session('currentCompany'))
        ->delete();

        $msg = trans('textos.agencia_desvinculada_ok');
        return redirect()->route('agencies.index')->with(compact('msg'));
    }
}
