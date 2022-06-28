<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Bank;
use App\Models\BankAccounts;

class BankAccountsController extends Controller{
    /**
     * 1. Relación de cuentas bancarias.
     * 2. Formulario nueva cuenta.
     * 3. Guardar nueva cuenta.
     * 4. Editar cuenta.
     * 5. Actualizar cuenta.
     * 6. Actualizar estado de cuenta.
     * 7. Eliminar cuenta.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:accounting');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'bank_id' => 'required|integer',
                'account' => 'required|max:25'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'bank_id' => 'required|integer',
                'account' => 'required|max:25'
            ]);
        }
    } 

    /**
     * 1. Relación de cuentas bancarias.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.bancos_cuentas',
            'module' => $this->module,
            'slug' => 'bank_accounts'
        ]; 

        $accounts = BankAccounts::select('bank_accounts.*', 'banks.name', 'banks.tradename')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->where('bank_accounts.company_id', session('currentCompany'))
        ->orderBy('banks.name')
        ->get();

        return view('admin.bank_accounts.index')->with(compact('header', 'accounts'));    
    }

    /**
     * 2. Formulario nueva cuenta.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.cuenta_nueva'),
            'module' => $this->module,
            'slug' => 'bank_accounts'
        ];

        $banks = Bank::getBanks(true);

        $accounts = BankAccounts::where('company_id', session('currentCompany'))
        ->get();

        return view('admin.bank_accounts.create')->with(compact('header', 'banks', 'accounts'));     
    }

    /**
     * 3. Guardar nueva cuenta.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/bank-accounts/create')
            ->withErrors($validator)
            ->withInput();    
        }

        $row = new BankAccounts();
        $row->company_id = session('currentCompany');
        $row->bank_id = $request->bank_id;
        $row->account = $request->account;
        $row->featured = $request->featured? true:false;
        $row->status = $request->status? true:false;
        $row->save();

        $msg = trans('textos.banco_cuenta_creada');
        return redirect()->route('bank-accounts.edit', $row)->with(compact('msg'));
    }

    /**
     * 4. Editar cuenta.
     */
    public function edit(BankAccounts $account){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.banco_cuenta_editar'),
            'module' => $this->module,
            'slug' => 'banks'
        ];

        $banks = Bank::getBanks(true);

        return view('admin.bank_accounts.edit')->with(compact('header', 'banks', 'account'));  
    }

    /**
     * 5. Actualizar cuenta.
     */
    public function update(Request $request, BankAccounts $account){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/bank-accounts/'.$account->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        $account->company_id = session('currentCompany');
        $account->bank_id = $request->bank_id;
        $account->account = $request->account;
        $account->featured = $request->featured? true:false;
        $account->status = $request->status? true:false;
        $account->save();

        $msg = trans('textos.banco_cuenta_updated');
        return redirect()->route('bank-accounts.edit', $account)->with(compact('msg'));
    }

    /**
     * 6. Actualizar estado de cuenta.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){
            $row = BankAccounts::find($id);
            $row->status = $status;
            $row->save();

            $resp = ($row)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar cuenta.
     */
    public function destroy(BankAccounts $account){
        $account->delete();   

        $msg = trans('textos.banco_cuenta_deleted');
        return redirect()->route('bank-accounts.index')->with(compact('msg')); 
    }
}
