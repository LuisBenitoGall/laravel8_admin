<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\AccountingAccount;
use App\Models\AccountingAccountType;
use App\Models\CompanySettings;

class AccountingAccountController extends Controller{
    /**
     * 1. Relación de cuentas contables.
     * 1.1. Lista de cuentas para DataTable.
     * 2. Formulario nueva cuenta.
     * 3. Guardar nueva cuenta.
     * 4. Editar cuenta.
     * 5. Actualizar cuenta.
     * 6. Eliminar cuenta.
     * 7. Estado de la cuenta.
     * 8. Autocomplete de cuentas.
     * 9. Marcar cuenta como principal.
     * 10. Marcar cuenta IVA.
     * 11. Cuentas por defecto.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])) {
            return Validator::make($data, [
                'company' => 'required_without:own_company',
                'account' => 'nullable|required_with:own_company|min:'.session('companySettings')->accounting_account_digits.'|max:'.session('companySettings')->accounting_account_digits,
                'name' => 'required',
                'type' => 'required'
            ],[
                'account.required_with' => trans('textos.cuenta_obligatoria_interna'),
                //'account.integer' => trans('textos.cuenta_numerica_aviso'),
                'account.min' => trans('textos.cuenta_consta').session('companySettings')->accounting_account_digits.' '.trans('textos.digitos'),
                'account.max' => trans('textos.cuenta_consta').session('companySettings')->accounting_account_digits.' '.trans('textos.digitos'),
                'company.required_without' => trans('textos.empresa_obligatoria_no_cuenta_interna')
            ]);

        //Validación update:
        } elseif (isset($data['submit_update'])) {
            return Validator::make($data, [
                'name' => 'required'
            ]);
        }
    }

    /**
     * 1. Relación de cuentas contables.
     */
    public function index(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.cuentas_contables',
            'module' => $this->module,
            'slug' => 'accounting-accounts'
        ];  

        $company = Company::find(session('currentCompany'));

        //Cuentas contables internas:
        $accounting_accounts = AccountingAccount::select(DB::raw("CONCAT(account,' - ',name) AS cuenta"), 'id')
        ->where('company_id', session('currentCompany'))
        ->where('company_target', session('currentCompany'))
        ->pluck('cuenta', 'id')
        ->toArray();

        $default_accounts = AccountingAccount::select('id', 'featured')
        ->where('company_id', session('currentCompany'))
        ->where('featured', '>', 0)
        ->pluck('id', 'featured')
        ->toArray();

        return view('admin.accounting_accounts.index')->with(compact('header', 'company', 'accounting_accounts', 'default_accounts'));
    }

    /**
     * 1.1. Lista de cuentas para DataTable.
     */
    public function getAccountingAccountsDT(){
        $data = AccountingAccount::select('accounting_accounts.*', 'companies.name AS company')
        ->leftJoin('companies', 'accounting_accounts.company_id', '=', 'companies.id')
        ->where('accounting_accounts.company_id', session('currentCompany'))
        ->orderBy('accounting_accounts.featured', 'DESC');

        return DataTables::of($data)
        ->addColumn('account', function ($row){
            return $row->account;
        }) 
        ->addColumn('name', function ($row){
            return $row->name;
        }) 
        ->addColumn('company', function ($row){
            return $row->company;
        })
        ->addColumn('tipo', function ($row){
            // $first_letter = substr($row->type, 0, 1);
            // return $row->type? ucfirst($types[$first_letter]):'';
        })
        ->addColumn('subtipo', function ($row){
            // $first_letter = substr($row->type, 0, 1);
            // $subtype_letters = substr($row->type, 1, 2);
            // $subtype = isset($subtypes[$first_letter][$subtype_letters])? $subtypes[$first_letter][$subtype_letters]:'';
            // return $subtype;
        })
        ->addColumn('with_iva', function ($row){
            if($row->with_iva){
                return '<i class="fa fa-check text-success"></i>';
            }else{
                return '<i class="fa fa-ban text-danger"></i>';
            }
        })
        ->addColumn('acciones', function ($row){
            return view('admin.accounting_accounts.partials.dt-actions', compact('row'));
        })
        ->rawColumns(['with_iva'])
        ->make(true);
    }

    /**
     * 2. Formulario nueva cuenta.
     */
    public function create(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.cuenta_contable_nueva',
            'module' => $this->module,
            'slug' => 'accounting-accounts'
        ]; 

        $languages = $this->languages;

        return view('admin.accounting_accounts.create')->with(compact('header', 'languages'));    
    }

    /**
     * 3. Guardar nueva cuenta.
     */
    public function store(Request $request){
        //dd($request->all());
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/accounting-accounts/create')
                ->withErrors($validator)
                ->withInput();
        }

        $company_target = $request->own_company? session('currentCompany'):$request->company_id;

        $type = $request->subtype? $request->type.$request->subtype:$request->type.'00';

        //Obtenemos última numeración de la cuenta:
        $last_account = AccountingAccount::select('account')
        ->where('company_id', session('currentCompany'))
        ->where('type', $type)
        ->orderBy('account', 'DESC')
        ->first();

        //Cantidad de dígitos de la cuenta, según cada empresa:
        $zeros = session('companySettings')->accounting_account_digits && session('companySettings')->accounting_account_digits > 0? session('companySettings')->accounting_account_digits:4;
        $digits = '';
        $i = 0;
        while($i < $zeros){
            $digits .= '0';
            $i++;
        }

        $last_account_ = $last_account? $last_account->account:$type.$digits;

        $account = (int)$last_account_ + 1;

        $aa = new AccountingAccount();
        $aa->company_id = session('currentCompany');
        $aa->account = $account;
        $aa->name = $request->name;
        $aa->type = $type;
        $aa->company_target = $company_target;
        $aa->status = $request->status? 1:0;
        $aa->with_iva = $request->with_iva? 1:0;
        $aa->created_by = Auth::user()->id;
        $aa->save();

        $msg = trans('textos.cuenta_creada');
        return redirect()->route('accounting-accounts.edit', $aa)->with(compact('msg'));
    }

    /**
     * 4. Editar cuenta.
     */
    public function edit(AccountingAccount $account){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.cuenta_contable_editar',
            'module' => $this->module,
            'slug' => 'accounting-accounts'
        ]; 

        //Empresa target:
        $company = Company::select('name')->find($account->company_target);

        $languages = $this->languages;

        return view('admin.accounting_accounts.edit')->with(compact('header', 'account', 'company', 'languages'));     
    }

    /**
     * 5. Actualizar cuenta.
     */
    public function update(Request $request, AccountingAccount $account){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/accounting-accounts/'.$account->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $account->name = $request->name;
        $account->status = $request->status? 1:0;
        $account->updated_by = Auth::user()->id;
        $account->save();

        $msg = trans('textos.cuenta_updated');
        return redirect()->route('accounting-accounts.edit', $account)->with(compact('msg'));
    }

    /**
     * 6. Eliminar cuenta.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountingAccount $account){
        $account_id = $account->id;
        
        $account->delete();  

        $msg = trans('textos.cuenta_deleted');
        return redirect()->route('accounting-accounts.index')->with(compact('msg'));   
    }

    /**
     * 7. Estado de la cuenta.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $account = AccountingAccount::find($id);
            $account->status = $status;
            $account->save();

            $resp = ($account)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 8. Autocomplete de cuentas.
     */
    public function getAccounts(Request $request){
        $search = $request->search;

        $data = AccountingAccount::getAccounts($search);  
        return response()->json($data); 
    }

    /**
     * 9. Marcar cuenta como principal.
     */
    public function featured(AccountingAccount $account){
        AccountingAccount::where('company_id', $account->company_id)
        ->where('type', 'i')
        ->update(['featured' => '0']);

        $account->featured = 1;
        $account->save();

        $msg = trans('textos.cuenta_principal_ok');
        return redirect()->route('accounting-accounts.index')->with(compact('msg'));
    }

    /**
     * 10. Marcar cuenta IVA.
     */
    public function ivaAccount(AccountingAccount $account){
        AccountingAccount::where('company_id', $account->company_id)
        ->where('mode', 'i')
        ->update(['iva_account' => '0']);

        $account->iva_account = 1;
        $account->save();

        $msg = trans('textos.cuenta_iva_ok');
        return redirect()->route('accounting-accounts.index')->with(compact('msg'));
    }

    /**
     * 11. Cuentas por defecto.
     */
    public function defaultAccounts(Request $request){
        //Eliminamos las cuentas default anteriores:
        AccountingAccount::where('company_id', session('currentCompany'))
        ->where('featured', '>', 0)
        ->update(['featured' => 0]);

        if($request->accounting_sales){
            AccountingAccount::where('id', $request->accounting_sales)
            ->update(['featured' => config('constants.ACCOUNT_DEFAULT_SALES_')]);   
        } 
        if($request->accounting_sales_iva){
            AccountingAccount::where('id', $request->accounting_sales_iva)
            ->update(['featured' => config('constants.ACCOUNT_DEFAULT_SALES_IVA_')]);   
        } 
        if($request->accounting_purchases){
            AccountingAccount::where('id', $request->accounting_purchases)
            ->update(['featured' => config('constants.ACCOUNT_DEFAULT_PURCHASES_')]);   
        } 
        if($request->accounting_purchases_iva){
            AccountingAccount::where('id', $request->accounting_purchases_iva)
            ->update(['featured' => config('constants.ACCOUNT_DEFAULT_PURCHASES_IVA_')]);   
        }  
        if($request->accounting_cash_sales){
            AccountingAccount::where('id', $request->accounting_cash_sales)
            ->update(['featured' => config('constants.ACCOUNT_DEFAULT_CASH_SALES_')]);   
        }  
        if($request->accounting_cash_purchases){
            AccountingAccount::where('id', $request->accounting_cash_purchases)
            ->update(['featured' => config('constants.ACCOUNT_DEFAULT_CASH_PURCHASES_')]);   
        }         

        $msg = trans('textos.cuentas_defecto_definidas');
        return redirect()->route('accounting-accounts.index')->with(compact('msg'));  
    }
}
