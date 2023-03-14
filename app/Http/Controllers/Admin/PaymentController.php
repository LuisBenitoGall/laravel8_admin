<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\CompanySettings;
use App\Models\Payment;

class PaymentController extends Controller{
    /**
     * 1. Cobros de facturas.
     * 1.1. Listado de cobros para DataTable.
     * 2. Guardar cobro.
     * 3. Actualizar cobro.
     * 4. Eliminar cobro.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $co_currency;
    private $languages;
    
    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:accounting');
        $this->middleware('invoice_post', ['only' => ['store', 'update']]);
        //$this->middleware('bank_accounts_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'invoice_id' => 'required|integer',
                'amount' => 'required|numeric'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'invoice_id' => 'required|integer',
                'payment_id' => 'required|integer',
                'amount' => 'required|numeric'
            ]);
        }
    }

    /**
     * 1. Cobros de facturas.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cobros',
            'module' => $this->module,
            'slug' => 'invoices'
        ];      
    }

    /**
     * 1.1. Listado de cobros para DataTable.
     */
    public function getPaymentsDT(){
        
    }

    /**
     * 2. Guardar cobro.
     */
    public function store(Request $request){

        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-payment');

            return redirect('/admin/invoices/'.$request->invoice_id.'/edit/payments')
                ->withErrors($validator)
                ->withInput();
        }

        

        $payment_date = $request->payment_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->payment_date):$request->payment_date:NULL;

        $row = new Payment();
        $row->company_from = $request->company_from; 
        $row->company_to = $request->company_to;
        $row->amount = $request->amount;
        $row->payment_date = $payment_date;
        $row->payment_method_id = $request->payment_method_id;
        $row->invoice_id = $request->invoice_id;
        $row->bank_account_id = $request->bank_account_id;
        $row->is_ticket = $request->is_ticket;
        $row->observations = $request->observations;
        $row->user_id = Auth::user()->id;
        $row->save();

        $msg = trans('textos.cobro_guardado');
        //Compra:
        if($row->company_from == session('currentCompany')){
            return redirect()->route('invoices.edit-purchase', [$request->invoice_id, 'payments'])->with(compact('msg'));
        //Venta:
        }else{
            return redirect()->route('invoices.edit', [$request->invoice_id, 'payments'])->with(compact('msg'));
        }
    }

    /**
     * 3. Actualizar cobro.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-payment');

            return redirect('/admin/invoices/'.$request->invoice_id.'/edit/payments')
                ->withErrors($validator)
                ->withInput();
        }   

        $row = Payment::find($request->payment_id);
        $row->amount = $request->amount;
        $row->payment_method_id = $request->payment_method;
        $row->bank_account_id = $request->bank_account_id;
        $row->observations = $request->observations;
        $row->save();

        $msg = trans('textos.cobro_updated');
        //Compra:
        if($row->company_from == session('currentCompany')){
            return redirect()->route('invoices.edit-purchase', [$request->invoice_id, 'payments'])->with(compact('msg'));
        //Venta:
        }else{
            return redirect()->route('invoices.edit', [$request->invoice_id, 'payments'])->with(compact('msg'));
        }
    }

    /**
     * 4. Eliminar cobro.
     */
    public function destroy(Request $request, Payment $payment){
        $invoice_id = $payment->invoice_id;
        $company_from = $payment->company_from;

        $payment->delete();

        $msg = trans('textos.cobro_deleted');
        //Compra:
        if($company_from == session('currentCompany')){
            return redirect()->route('invoices.edit-purchase', [$invoice_id, 'payments'])->with(compact('msg'));
        //Venta:
        }else{
            return redirect()->route('invoices.edit', [$invoice_id, 'payments'])->with(compact('msg'));
        }
    }
}
