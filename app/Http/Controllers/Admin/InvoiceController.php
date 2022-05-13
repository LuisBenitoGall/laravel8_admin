<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Notifications\PurchaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Invoice;
use App\Models\Order;

class InvoiceController extends Controller{
    /**
     * 1. Facturas de clientes.
     * 1.1. Listado de facturas para DataTable.
     * 2. Generar factura desde pedido.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        //IVA:
        $this->co_iva = CompanySettings::co_iva();

        $this->middleware('module_setted:accounting');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación generación de factura:
        if(isset($data['submit_generate_invoice'])){
            $data['advance_amount'] = isset($data['advance_amount'])? $data['advance_amount']:'';
            $data['advance_percent'] = isset($data['advance_percent'])? $data['advance_percent']:'';

            return Validator::make($data, [
                'order_id' => 'required|integer',   
                'modality' => 'required',
                'advance_amount' => 'nullable|validate_third_part:p,'.$data['modality'].','.$data['advance_percent'],
                'advance_percent' => 'nullable|validate_third_part:p,'.$data['modality'].','.$data['advance_amount']
            ],[
                'advance_amount.validate_third_part' => trans('textos.required_avance_factura'),
                'advance_percent.validate_third_part' => trans('textos.required_avance_factura')
            ]);

        }
    }

    /**
     * 1. Facturas por cliente o proveedor.
     */
    public function index($year = false, $customer_id = false, $provider_id = false){
        if($customer_id && $customer_id == session('currentCompany')){
            $invoicesType = 'facturas_compras';
        }elseif($provider_id && $provider_id == session('currentCompany')){
            $invoicesType = 'facturas_ventas';
        }else{
            $invoicesType = 'facturas';
        }   
        
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$invoicesType,
            'module' => $this->module,
            'slug' => 'invoices'
        ];  

        if(!$year) $year = Date('Y');

        $customer = $customer_id? Company::find($customer_id):false;
        $provider = $provider_id? Company::find($provider_id):false;

        $customers = false;
        $providers = false;

        //Comprobamos si la empresa hace gestión de clientes:
        if(session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }

        //Comprobamos si la empresa hace gestión de proveedores:
        if(session('companySettings')['providers_management'] == 1){
            $providers = CustomerProviders::getCompanyProviders(session('currentCompany'));
        }

        return view('admin.invoices.index')->with(compact('header', 'year', 'customer_id', 'provider_id', 'customer', 'provider', 'customers', 'providers'));
    }

    /**
     * 1.1. Listado de facturas para DataTable.
     */

    /**
     * 2. Generar factura desde pedido.
     */
    public function createFromOrder(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-generate-invoice');

            return redirect('/admin/orders/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $order = Order::find($request->order_id);

        dd($request->all(), $order);

        //Generando factura:
        $invoice = new Invoice();
        $invoice->user_id = Auth::user()->id;
        $invoice->save();

        $msg = trans('textos.factura_creada');
        return redirect()->route('invoices.edit', [$invoice->id, false])->with(compact('msg'));
    }
}
