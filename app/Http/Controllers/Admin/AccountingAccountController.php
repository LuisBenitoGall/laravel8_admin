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
use App\Models\CustomerProviders;
use App\Models\IvaType;

class AccountingAccountController extends Controller{
    /**
     * 1. Relación de cuentas contables.
     * 1.1. Lista de cuentas para DataTable.
     * 2. Formulario nueva cuenta.
     * 3. Guardar nueva cuenta.
     * 3.1. Guardar nueva cuenta para clientes y proveedores.
     * 4. Editar cuenta.
     * 5. Actualizar cuenta.
     * 6. Eliminar cuenta.
     * 6.1. Eliminar cuenta de IVA.
     * 7. Estado de la cuenta.
     * 8. Autocomplete de cuentas.
     * 9. Marcar cuenta como principal.
     * 10. Marcar cuenta IVA.
     * 10.1. Cuentas IVA.
     * 11. Cuentas por defecto.
     * 12. Generación masiva de cuentas contables.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;
    private $accounting_account_digits;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Dígitos de cuentas contables:
        $this->accounting_account_digits = CompanySettings::co_account_digits();
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])) {
            //Se suman 4 dígitos que corresponden a los tipos de cuentas contables según el Plan General de Contabilidad:
            $total_digits = $this->accounting_account_digits + 4;

            return Validator::make($data, [
                //'company' => 'required_if:mode,a|required_if:mode,p|required_if:mode,c',
                'account' => 'nullable|required_without:level1|digits:'.$total_digits.'|integer',
                'name' => 'required_without:accounting_account_id',
                'type' => 'required_without:accounting_account_id',
                'accounting_account_id' => 'required_without_all:name,type'
                //'digits' => 'required_with:level1|digits:'.$this->accounting_account_digits
            ],[
                //'account.min' => trans('textos.cuenta_consta').session('companySettings')->accounting_account_digits.' '.trans('textos.digitos'),
                'account.integer' => trans('textos.cuenta_numerica_aviso'),
                'account.digits' => trans('textos.cuenta_consta').$total_digits.' dígitos',
                'account.required_without' => trans('textos.cuenta_contable_digitos_aviso'),
                //'digits.digits' => 'El resto de la numeración debe constar de '.$this->accounting_account_digits.' dígitos'
                'accounting_account_id.required_without_all' => trans('textos.cuenta_obligatoria')
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

        //Generación masiva de cuentas:
        //$this->generateAllAccounts(); 

        $company = Company::find(session('currentCompany'));

        //Cuentas contables internas:
        // $accounting_accounts = AccountingAccount::select(DB::raw("CONCAT(account,' - ',name) AS cuenta"), 'id')
        // ->where('company_id', session('currentCompany'))
        // ->where('company_target', session('currentCompany'))
        // ->pluck('cuenta', 'id')
        // ->toArray();

        $default_accounts_ = AccountingAccount::select('id', 'account', 'name', 'featured')
        ->where('company_id', session('currentCompany'))
        ->where('featured', '>', 1)
        ->get();
        $default_accounts = [];
        foreach($default_accounts_ as $da){
            $default_accounts[$da->featured] = [$da->id, $da->account.' - '.$da->name];    
        }

        return view('admin.accounting_accounts.index')->with(compact('header', 'company', 'default_accounts'));
    }

    /**
     * 1.1. Lista de cuentas para DataTable.
     */
    public function getAccountingAccountsDT(){
        $data = AccountingAccount::select('accounting_accounts.*', 'companies.name AS company')
        ->leftJoin('companies', 'accounting_accounts.company_target', '=', 'companies.id')
        ->leftJoin('accounting_account_types', 'accounting_accounts.aatype_id', '=', 'accounting_account_types.id')
        ->where('accounting_accounts.company_id', session('currentCompany'))
        ->orderBy('accounting_accounts.featured', 'DESC');

        $tipos = helpers::accountingAccountTypes();

        $modes = helpers::accountingAccountModes();

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
        ->addColumn('tipo', function ($row) use($tipos){
            if(isset($tipos[$row->type])){
                return ucfirst($tipos[$row->type]);   
            }
        })
        ->addColumn('mode', function ($row) use($modes){
            return ucfirst($modes[$row->mode]);
        })
        ->filterColumn('tipo', function ($query, $keyword) use ($tipos) {
            //dd($keyword);
            $query->whereIn('type', array_keys(array_filter($tipos, function ($type) use ($keyword) {
                return stripos($type, $keyword) !== false;
            })));
        })


        // ->filterColumn('tipo', function ($query, $keyword) use($types){
        //     $query->whereIn('type', array_keys(array_filter($types, function ($value) use ($keyword) {
        //         return stripos($value, $keyword) !== false;
            


        //     //$query->where('type', $types[$keyword]);
        // })))
        // ->addColumn('subtipo', function ($row){
        //     // $first_letter = substr($row->type, 0, 1);
        //     // $subtype_letters = substr($row->type, 1, 2);
        //     // $subtype = isset($subtypes[$first_letter][$subtype_letters])? $subtypes[$first_letter][$subtype_letters]:'';
        //     // return $subtype;
        // })
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

        $types = AccountingAccountType::select(DB::raw("CONCAT(code, ' - ', name) as type"), 'id')
        ->where('autoreference', '0')
        ->orderBy('code', 'ASC')
        ->pluck('type', 'id')
        ->toArray();

        $modes = helpers::accountingAccountModes();

        $tipos = helpers::accountingAccountTypes();

        $languages = $this->languages;

        $company_settings = CompanySettings::where('company_id', session('currentCompany'))->first();

        return view('admin.accounting_accounts.create')->with(compact('header', 'types', 'modes', 'tipos', 'languages', 'company_settings'));    
    }

    /**
     * 3. Guardar nueva cuenta.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            if($request->return == 'iva-accounts'){
                $validator->getMessageBag()->add('accion', 'modal-add-account');

                return redirect('/admin/accounting-accounts/iva-accounts')
                ->withErrors($validator)
                ->withInput();    
            }else{
                return redirect('/admin/accounting-accounts/create')
                ->withErrors($validator)
                ->withInput();
            }
        }

        //Para IVA, si se ha seleccionado una cuenta ya existente:
        if($request->accounting_account_id){
            $prev_account = AccountingAccount::select('id', 'account')
            ->find($request->accounting_account_id);

            //Validamos el formato de la cuenta para IVA:
            $pfx = $prev_account? substr($prev_account->account, 0, 4):false;

            if($pfx == config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_SALES_') || $pfx == config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_PURCHASES_')){
                $prev_account->iva_type_id = $request->iva_type_id;
                $prev_account->save();
            }else{
                $alert = trans('textos.cuenta_iva_error', ['account' => $prev_account->account]);
                return redirect()->route('accounting-accounts.iva-accounts')->with(compact('alert'));
                exit;
            }

        //Generación nueva cuenta:
        }else{
            //Generación manual:
            if($request->account){
                $account = $request->account;

            //Cuentas de IVA:
            }elseif($request->iva_type_id){
                //Separamos el prefijo del porcentaje de iva:
                $p_ = explode('/', $request->prefix);

                //Nº de dígitos del porcentaje de iva:
                $n_digits = strlen($p_[1]);
                //Resto de 0:
                $rest0 = $this->accounting_account_digits - $n_digits;
                //Componiendo número de cuenta:
                $account = $p_[0];
                $z = 1;
                while($z <= $rest0){
                    $account .= '0';
                    $z++;
                }
                $account .= $p_[1];

            //Generación automática de la cuenta:
            }else{
                $level_id = $request->level1;
                $zeros = '000';
                if($request->level4){
                    $level_id = $request->level4;
                    $zeros = '';
                }elseif($request->level3){
                    $level_id = $request->level3;
                    $zeros = '0';
                }elseif($request->level2){
                    $level_id = $request->level2;
                    $zeros = '00';
                }

                $account_type = AccountingAccountType::select('id', 'autoreference', 'code')
                ->find($level_id);

                $account_prefix = $account_type->code.$zeros;

                //Resto de la numeración:
                if($request->digits){
                    $account = $account_prefix.$request->digits;

                }else{
                    //Obtenemos la última cuenta de esta tipología:
                    // $last_account = AccountingAccount::select('id', 'account')
                    // ->where('company_id', session('currentCompany'))
                    // ->where('account', 'LIKE', $account_prefix.'%')
                    // ->first(); 
                    $last_account = AccountingAccount::getLastAccount($account_prefix, session('currentCompany'));         

                    if($last_account && $last_account->account){
                        $account = (int)$last_account->account + 1;   
                    }else{
                        $pre_zeros = '';
                        $z = 1;
                        while($z < $this->accounting_account_digits){
                            $pre_zeros .= '0';
                            $z++;
                        }
                        $account = $account_prefix.$pre_zeros.'1';
                    }
                }
            }

            //Comprobamos que no exista la cuenta:
            $exist_account = AccountingAccount::select('id')
            ->where('company_id', session('currentCompany'))
            ->where('account', $account)
            ->first();

            if($exist_account){
                $alert = trans('textos.cuenta_contable_existe', ['account' => $account]);
                if($request->return == 'iva-accounts'){
                    return redirect()->route('accounting-accounts.iva-accounts')->with(compact('alert'));    
                }else{
                    return redirect()->route('accounting-accounts.create')->with(compact('alert'));
                }
                exit;
            }

            $company_id = $request->company_id? $request->company_id:session('currentCompany');

            //Indicador de cuentas de ganancias y pérdidas para los grupos 6 y 7:
            $first_ = substr($account, 0, 1);
            $earnings_loses = $first_ == 6 || $first_ == 7? true:false;

            // $company_target = $request->own_company? session('currentCompany'):$request->company_id;

            // $type = $request->subtype? $request->type.$request->subtype:$request->type.'00';

            // //Obtenemos última numeración de la cuenta:
            // $last_account = AccountingAccount::select('account')
            // ->where('company_id', session('currentCompany'))
            // ->where('type', $type)
            // ->orderBy('account', 'DESC')
            // ->first();

            // //Cantidad de dígitos de la cuenta, según cada empresa:
            // $zeros = session('companySettings')->accounting_account_digits && session('companySettings')->accounting_account_digits > 0? session('companySettings')->accounting_account_digits:4;
            // $digits = '';
            // $i = 0;
            // while($i < $zeros){
            //     $digits .= '0';
            //     $i++;
            // }

            // $last_account_ = $last_account? $last_account->account:$type.$digits;

            // $account = (int)$last_account_ + 1;

            if(!$exist_account){
                $aa = new AccountingAccount();
                $aa->company_id = session('currentCompany');
                $aa->account = $account;
                $aa->name = $request->name;
                $aa->mode = $request->mode;
                $aa->type = $request->type;
                $aa->earnings_loses = $earnings_loses;
                $aa->company_target = $company_id;
                $aa->status = $request->status? 1:0;
                $aa->with_iva = $request->with_iva? 1:0;
                $aa->iva_type_id = $request->iva_type_id;
                $aa->created_by = Auth::user()->id;
                $aa->save();
            }
        }

        $msg = trans('textos.cuenta_creada');
        if($request->return == 'iva-accounts'){
            return redirect()->route('accounting-accounts.iva-accounts')->with(compact('msg'));    
        }else{
            return redirect()->route('accounting-accounts.edit', $aa)->with(compact('msg'));
        }
    }

    /**
     * 3.1. Guardar nueva cuenta para clientes y proveedores.
     */
    public function storeAuto(Request $request){
        $account_to = $request->mode == 1? 'customer':'provider';
        $return = $request->mode == 1? 'customers':'providers';

        //Creando cuenta contable:
        $accounting_account_data = [
            'account_to' => $account_to,
            'company_target' => $request->company_target,
            'mode' => $request->mode,
            'with_iva' => 1
        ];
        AccountingAccount::setAccountingAccount($accounting_account_data);

        $msg = trans('textos.cuenta_creada');
        return redirect()->route($return.'.edit', [$request->company_target, 'accounting-accounts'])->with(compact('msg'));
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

        $modes = helpers::accountingAccountModes();

        //Empresa target:
        $company = Company::select('name')->find($account->company_id);

        $languages = $this->languages;

        return view('admin.accounting_accounts.edit')->with(compact('header', 'account', 'modes', 'company', 'languages'));  
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
    public function destroy(Request $request, AccountingAccount $account){
        $account_id = $account->id;
        $company_target = $account->company_target;
        
        $account->delete();  

        $msg = trans('textos.cuenta_deleted');
        if($request->return){
            return redirect()->route($request->return.'.edit', [$company_target, 'accounting-accounts'])->with(compact('msg'));
        }else{
            return redirect()->route('accounting-accounts.index')->with(compact('msg'));   
        }
    }

    /**
     * 6.1. Eliminar cuenta de IVA.
     */
    public function destroyIVA(AccountingAccount $account){
        $account->delete();

        $msg = trans('textos.cuenta_deleted');
        return redirect()->route('accounting-accounts.iva-accounts')->with(compact('msg'));
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
        $data = AccountingAccount::getAccounts($request->search);  
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
     * 10.1. Cuentas IVA.
     */
    public function ivaAccounts(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.cuentas_iva',
            'module' => $this->module,
            'slug' => 'accounting-accounts'
        ]; 

        $modes = helpers::accountingAccountModes();

        $iva_types = IvaType::where('status', 1)->orderBy('iva', 'ASC')->get();

        //Cuentas de IVA:
        $accounts = AccountingAccount::select('id', 'account', 'name', 'mode', 'type', 'iva_type_id')
        ->whereNotNull('iva_type_id')
        ->where('status', 1)
        ->get();

        //Tipos de cuentas contables:
        $tipos = helpers::accountingAccountTypes();

        $languages = $this->languages;

        return view('admin.accounting_accounts.iva-accounts')->with(compact('header', 'modes', 'iva_types', 'accounts', 'tipos', 'languages'));      
    }

    /**
     * 11. Cuentas por defecto.
     */
    public function defaultAccounts(Request $request){
        //dd($request->all());
        //Eliminamos las cuentas default anteriores:
        AccountingAccount::where('company_id', session('currentCompany'))
        ->where('featured', '>', 1)
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

    /**
     * 12. Generación masiva de cuentas contables.
     */
    public function generateAllAccounts(){
        $companies = [4,8,202,227,275];

        foreach($companies as $c){
            //Clientes:
            $customers = CustomerProviders::select('customer_id')
            ->where('provider_id', $c)
            ->get();

            foreach($customers as $cu){
                //Comprobamos que no exista:
                $existC = AccountingAccount::select('id')
                ->where('company_id', $c)
                ->where('company_target', $cu->customer_id)
                ->where('type', 'c')
                ->first();

                if(!$existC){
                    $accounting_account_dataC = [
                        'account_to' => 'customer',
                        'company_target' => $cu->customer_id,
                        'mode' => 1,
                        'with_iva' => 1
                    ];
                    AccountingAccount::setAccountingAccount($accounting_account_dataC, $c);  
                }  
            }

            //Proveedores:
            $providers = CustomerProviders::select('provider_id')
            ->where('customer_id', $c)
            ->get();

            foreach($providers as $pr){
                //Comprobamos que no exista:
                $existP = AccountingAccount::select('id')
                ->where('company_id', $c)
                ->where('company_target', $pr->provider_id)
                ->where('type', 'p')
                ->first();

                if(!$existP){
                    $accounting_account_dataP = [
                        'account_to' => 'provider',
                        'company_target' => $pr->provider_id,
                        'mode' => 2,
                        'with_iva' => 1
                    ];
                    AccountingAccount::setAccountingAccount($accounting_account_dataP, $c); 
                }   
            }
        }
    }
}
