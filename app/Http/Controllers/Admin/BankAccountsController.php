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
use App\Models\AccountingAccount;
use App\Models\Bank;
use App\Models\BankAccounts;

class BankAccountsController extends Controller{
    /**
     * 1. Relación de cuentas bancarias.
     * 2. Formulario nueva cuenta.
     * 3. Guardar nueva cuenta.
     * 4. Editar cuenta.
     * 5. Actualizar cuenta.
     * 5.1. Actualizar cuenta desde modal.
     * 6. Actualizar estado de cuenta.
     * 7. Eliminar cuenta.
     * 7.1. Eliminar cuenta desde modal.
     * 8. Actualizar cuenta principal.
     * 9. Cuentas por empresa.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;
    private $accounting_account_mode = 3;   //Mode: 3 (bancos)

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:accounting');
        $this->middleware('bank_accounts_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'bank_id' => 'required|integer',
                //'account' => 'required|max:25',
                // 'iban' => 'required|max:4',
                // 'entity' => 'required|max:4',
                // 'office' => 'required|max:4',
                // 'dc' => 'required|max:2',
                // 'digits' => 'required|max:10'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'bank_id' => 'required|integer',
                'iban' => 'required|max:4',
                'entity' => 'required|max:4',
                'office' => 'required|max:4',
                'dc' => 'required|max:2',
                'digits' => 'required|max:10'
            ]);

        //Validación update modal:
        }elseif(isset($data['submit_update_'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'company_id' => 'required|integer',
                'bank_id' => 'required|integer',
                //'account' => 'required|max:25'
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

        //Conversión de formato de cuentas bancarias:
        //$this->conversionAccount();

        $accounts = BankAccounts::select('bank_accounts.*', 'banks.name', 'banks.tradename', 'accounting_accounts.account AS accounting_account')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->leftJoin('accounting_accounts', 'bank_accounts.accounting_account_id', '=', 'accounting_accounts.id')
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

        $banks = Bank::getBanks(1);

        $accounts = BankAccounts::where('company_id', session('currentCompany'))
        ->get();

        //Cuentas contables:
        $accounting_accounts = AccountingAccount::arrayAccountingAccounts(session('currentCompany'), $this->accounting_account_mode); 

        return view('admin.bank_accounts.create')->with(compact('header', 'banks', 'accounts', 'accounting_accounts'));     
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

        $companyId = $request->company_id? $request->company_id:session('currentCompany');

        if($request->origin == 'modal'){
            $data_account = helpers::splitBankAccount($request->account);

            if(!empty($data_account)){
                $iban = $data_account['iban'];
                $entity = $data_account['entity'];
                $office = $data_account['office'];
                $dc = $data_account['dc'];
                $digits = $data_account['digits'];    
            }else{
                $iban = '';
                $entity = '';
                $office = '';
                $dc = '';
                $digits = '';   
            }   

        }else{
            $iban = $request->iban;
            $entity = $request->entity;
            $office = $request->office;
            $dc = $request->dc;
            $digits = $request->digits;
        }   

        $account = strtoupper($iban).' '.$entity.' '.$office.' '.$dc.' '.$digits;     

        $row = new BankAccounts();
        $row->company_id = $companyId;
        $row->accounting_account_id = $request->accounting_account;
        $row->bank_id = $request->bank_id;
        $row->account = $account;
        $row->iban = strtoupper($iban);
        $row->entity = $entity;
        $row->office = $office;
        $row->dc = $dc;
        $row->digits = $digits;
        $row->featured = $request->featured? true:false;
        $row->status = $request->status? true:false;
        $row->save();

        $msg = trans('textos.banco_cuenta_creada');

        if($request->return){
            return redirect()->route($request->return.'.edit', [$companyId, 'banks'])->with(compact('msg')); 
        }else{
            return redirect()->route('bank-accounts.edit', $row)->with(compact('msg'));    
        }
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

        $banks = Bank::getBanks(1);

        //Cuentas contables:
        $accounting_accounts = AccountingAccount::arrayAccountingAccounts(session('currentCompany'), $this->accounting_account_mode); 

        return view('admin.bank_accounts.edit')->with(compact('header', 'banks', 'account', 'accounting_accounts'));  
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

        $account_num = strtoupper($request->iban).' '.$request->entity.' '.$request->office.' '.$request->dc.' '.$request->digits;

        $account->accounting_account_id = $request->accounting_account;
        $account->bank_id = $request->bank_id;
        $account->account = $account_num;
        $account->iban = strtoupper($request->iban);
        $account->entity = $request->entity;
        $account->office = $request->office;
        $account->dc = $request->dc;
        $account->digits = $request->digits;
        $account->featured = $request->featured? true:false;
        $account->status = $request->status? true:false;
        $account->save();

        $msg = trans('textos.banco_cuenta_updated');
        return redirect()->route('bank-accounts.edit', $account)->with(compact('msg'));
    }

    /**
     * 5.1. Actualizar cuenta desde modal.
     */
    public function updateFromModal(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/'.$request->return.'/'.$request->company_id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }    

        $account = BankAccounts::find($request->id);
        $account->bank_id = $request->bank_id;
        $account->account = $request->account;
        $account->save();

        $msg = trans('textos.banco_cuenta_updated');
        return redirect()->route($request->return.'.edit', [$request->company_id, 'banks'])->with(compact('msg'));
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

    /**
     * 7.1. Eliminar cuenta desde modal.
     */
    public function destroyFromModal(Request $request, BankAccounts $account){
        $company_id = $account->company_id;
        
        $account->delete();

        $msg = trans('textos.banco_cuenta_deleted');
        return redirect()->route($request->return.'.edit', [$company_id, 'banks'])->with(compact('msg'));
    }

    /**
     * 8. Actualizar cuenta principal.
     */
    public function featured(BankAccounts $account, $return = false){
        BankAccounts::where('company_id', $account->company_id)
        ->update(['featured' => false]);

        $account->featured = true;
        $account->save();

        $msg = trans('textos.banco_cuenta_updated');
        if($return){
            return redirect()->route($return.'.edit', [$account->company_id, 'banks'])->with(compact('msg'));
        }else{

        }
    }

    /**
     * 9. Cuentas por empresa.
     */
    public function companyAccounts(Request $request, $company_id){
        if($request->ajax()){ 
            $data = BankAccounts::getBankAccounts($company_id);
            return response()->json($data);
        }    
    }

    /**
     * 10. Conversión formato cuentas.
     */
    public function conversionAccount(){
        $data = BankAccounts::whereNull('iban')->get();

        if($data->count()){
            foreach($data as $row) {
                // if(strlen($row->account) !== 24){
                //     return false; // La cuenta no tiene la longitud esperada
                // }
                
                $account = str_replace(' ', '', $row->account);

                $iban = substr($account, 0, 4);
                $entity = substr($account, 4, 4);
                $office = substr($account, 8, 4);
                $dc = substr($account, 12, 2);
                $digits = substr($account, 14, 10);

                $account_ = strtoupper($iban).' '.$entity.' '.$office.' '.$dc.' '.$digits;

                $row->account = $account_;
                $row->iban = $iban;
                $row->entity = $entity;
                $row->office = $office;
                $row->dc = $dc;
                $row->digits = $digits;    
                $row->save();
            }
        }
    }
}
