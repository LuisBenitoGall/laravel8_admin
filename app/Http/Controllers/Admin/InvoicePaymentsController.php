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
use App\Models\InvoicePayments;

class InvoicePaymentsController extends Controller{
    /**
     * 1. Cobros de facturas.
     * 1.1. Listado de cobros para DataTable.
     * 2. Guardar cobro.
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
        $this->middleware('invoice_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'invoice_id' => 'required|integer',
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

        $row = new InvoicePayments();
        $row->invoice_id = $request->invoice_id;
        $row->amount = $request->amount;
        $row->mode = $request->mode;
        $row->user_id = Auth::user()->id;
        $row->observations = $request->observations;
        $row->save();

        $msg = trans('textos.cobro_guardado');
        return redirect()->route('invoices.edit', [$request->invoice_id, 'payments'])->with(compact('msg'));
    }
}
