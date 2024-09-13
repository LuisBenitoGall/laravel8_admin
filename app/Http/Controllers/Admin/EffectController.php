<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

use Carbon\Carbon;

//Models:
use App\Models\AccountingAccount;
use App\Models\BankAccounts;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Effect;
use App\Models\Invoice;
use App\Models\InvoiceEffect;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Remittance;
use App\Models\User;

class EffectController extends Controller{
    /**
     * 1. Relación de efectos.
     * 1.1. Filtro de efectos.
     * 1.2. Listado de efectos para DataTable.
     * 2. Formulario nuevo efecto.
     * 3. Guardar efecto.
     * 4. Editar efecto.
     * 5. Actualizar efecto.
     * 6. Eliminar efecto.
     * 7. Actualizar estado de efecto.
     * 8. Vincular a remesa.
     * 9. Vincular facturas.
     * 10. Efectos agrupados.
     * 10.1. Listado de efectos agrupados para DataTable.
     * 11. Filtro para agrupaciones de efectos.
     * 12. Efectos por cuenta contable.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;
    private $currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->currency = CompanySettings::co_currency(0);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación index:
        if (isset($data['submit_filter'])) {
            //Si no viene declarado order_id
            //if (!$data['effect']) {
                return Validator::make($data, [
                    //'year' => 'required|integer',
                    'customer_id' => 'required_without:provider_id|required_if:provider_id,==,0',
                    'provider_id' => 'required_without:customer_id|required_if:customer_id,==,0'
                ], [
                    'customer_id.required_without' => trans('textos.required_cliente_proveedor'),
                    'customer_id.required_if' => trans('textos.required_cliente_proveedor'),
                    'provider_id.required_without' => trans('textos.required_cliente_proveedor'),
                    'provider_id.required_if' => trans('textos.required_cliente_proveedor')
                ]);
            /*} else {
                return Validator::make($data, [
                    'effect' => 'required'
            ]); */
        //}

        //Validación guardar efecto:
        }elseif(isset($data['submit_save'])){
            return Validator::make($data, [
                'date' => 'required',
                'customer_id' => 'required_without:provider_id',
                'provider_id' => 'required_without:customer_id',
                'my_bank_account' => 'required|integer',
                'bank_account' => 'required|integer',
                'payment_method' => 'required|integer',
                'effects' => 'required_if:origin,==,group'
            ]);

        //Validación actualizar efecto:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'date' => 'required_without:update',
                'status' => 'required|integer',
                'payment_method' => 'required_without:update|integer',
                'my_bank_account' => 'required_without:update|integer',
                'bank_account' => 'required_without:update|integer',
            ]);

        //Validación remesa:
        }elseif(isset($data['submit_remittance'])){
            return Validator::make($data, [
                'effect_id' => 'required|integer',
                'remittance' => 'required|integer'
            ]);

        //Validación facturas:
        }elseif(isset($data['submit_add_invoices'])){
            return Validator::make($data, [
                'effect_id' => 'required|integer',
                'invoices' => 'required'
            ]);
        }
    }

    /**
     * 1. Relación de efectos.
     */
    public function index($customer_id = false, $provider_id = false){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.efectos',
            'module' => $this->module,
            'slug' => 'effects'
        ];

        $customer = $customer_id? Company::find($customer_id):false;
        $provider = $provider_id? Company::find($provider_id):false;

        $remittances = Remittance::select(DB::raw("CONCAT('Remesa ', id) AS remittance"), 'id')
        ->where('company_id', session('currentCompany'))
        ->where('status', config('constants.REMITTANCE_STATUS_OPEN_'))
        ->pluck('remittance', 'id')
        ->toArray();

        return view('admin.effects.index', compact('header', 'customer', 'provider', 'remittances'));
    }

    /**
     * 1.1. Filtro de efectos.
     */
    public function getEffectsBy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()) {
        return redirect('/admin/effects/list')
        ->withErrors($validator)
        ->withInput();
        }

        //Si viene declarado el efecto, redirigimos:
        if ($request->effect){
            $search = $request->effect;
            $currentCompany = session('currentCompany');

            //Comprobamos si el valor recibido corresponde a id, invoice_num o old_invoice:
            $effect = Effect::select('id')
            ->where('id', '>=', 1)
            ->where(function ($q) use ($search) {
                $q->where('id', $search)
                ->orWhere('effect_num', $search);
            })
            ->where(function ($q1) use ($currentCompany) {
                $q1->where('customer_id', $currentCompany)
                ->orWhere('provider_id', $currentCompany);
            })
            ->first();

            if(!$effect){
                $alert = trans('textos.efecto_no_existe');
                return redirect()->route('effects.index')->with(compact('alert'));
                exit;
            }else{
                return redirect()->route('effects.edit', $effect->id);
                exit;
            }
        }

        //Si no se informa de proveedor, éste es la empresa en sesión:
        $provider_id = $request->provider_id ? $request->provider_id : session('currentCompany');
        $customer_id = $request->customer_id ? $request->customer_id : session('currentCompany');

        //Sólo podemos tomar un criterio, nunca ambos:
        if($request->customer_id){
            $provider_id = session('currentCompany');
        }elseif($request->provider_id){
            $customer_id = session('currentCompany');
        }

        return redirect()->route('effects.index', [$customer_id, $provider_id]);
    }

    /**
     * 1.2. Listado de efectos para DataTable.
     */
    public function getEffectsDT($customer_id = false, $provider_id = false){
        if($customer_id == session('currentCompany')){
            $company_id = $provider_id;
            $company_field = 'provider';
        }else{
            $company_id = $customer_id; 
            $company_field = 'customer'; 
        }

        $estados = helpers::effectsStatus(); 

        $data = Effect::select('effects.*', 'currencies.symbol', 'users.name', 'users.surname', 'companies.name AS company', 'remittances.id AS remittanceId', 'remittances.remittance_num', 'remittances.remittance_old_num', 'payment_methods.name AS method')
        ->leftJoin('currencies', 'effects.currency_id', '=', 'currencies.id')
        ->join('users', 'effects.created_by', '=', 'users.id')
        ->join('companies', 'effects.'.$company_field.'_id', '=', 'companies.id')
        ->leftJoin('remittances', 'effects.remittance_id', '=', 'remittances.id')
        ->leftJoin('payment_methods', 'effects.payment_method_id', '=', 'payment_methods.id')
        //->whereYear('effects.created_at', '=', $year)
        ->whereNull('effects.deleted_at')
        ->when($provider_id && $customer_id && $customer_id > 0, function ($q) use ($customer_id, $provider_id) {
            return $q->where('effects.customer_id', $customer_id)
            ->where('effects.provider_id', $provider_id);
        });

        return DataTables::of($data)
        ->addColumn('num', function ($row){
            return $row->effect_num? $row->effect_num:$row->id;
        })
        ->addColumn('expiry_date', function ($row){
            return Carbon::parse($row->date)->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('company', function ($row){
            return $row->company;
        })
        ->addColumn('is_group', function ($row){
            if($row->is_group){
                return '<span style="display: none">1</span><a href="/admin/effects/'.$row->parent.'/edit">'.$row->parent.'</a>';
            }else{
                return '<span style="display: none">0</span>';
            }
        })
        ->addColumn('invoices', function ($row){
            $invoices = $row->invoices;
            $k = 0;
            $html = '';
            foreach($invoices as $inv){
                if($k > 0) $html .= ', ';
                $num_ = $inv->invoice_num? $inv->invoice_num:($inv->invoice_old_num? $inv->invoice_old_num:$inv->id);
                $html .= '<a target="_blank" href="'.route('invoices.edit', $inv->id).'">'.$num_.'</a>';
            }
            return $html;
        })
        ->addColumn('amount', function ($row){
            return $row->amount.$row->symbol;
        })
        ->addColumn('payment_method', function ($row){
            if($row->method){
                return $row->method;
            }else{
                return '';
            }
        })
        ->addColumn('seats', function ($row){
            $seats_ = '';
            $seats = $row->seats;
            if($seats->count()){
                foreach($seats as $s){
                    $seat_num = $s->seat_num? $s->seat_num:$s->id;
                    $seats_ .= '<a href="admin/seats/'.$s->id.'/edit">'.$seat_num.'</a> ';
                }
            }
            return $seats_;
        })
        ->addColumn('remittance', function ($row){
            if($row->remittanceId){
                $remittance = $row->remittance_num? $row->remittance_num:($row->remittance_old_num? $row->remittance_old_num:$row->remittanceId);
                return '<a target="_blank" href="'.route('remittances.edit', $row->remittanceId).'">'.$remittance.'</a>';
            }else{
                return '';
            }
        })
        ->addColumn('observations', function ($row){
            return $row->observations;
        })
        ->addColumn('estado', function ($row) use ($estados) {
            return view('admin.invoices.partials.dt-status', compact('row', 'estados'));
        })
        ->addColumn('acciones', function ($row) use($estados){
            return view('admin.effects.partials.dt-actions', compact('row', 'estados')); 
        })
        ->rawColumns(['is_group', 'invoices', 'seats', 'remittance', 'observations'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo efecto.
     */
    public function create(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.efecto_nuevo',
            'module' => $this->module,
            'slug' => 'effects'
        ];

        //Cuentas bancarias:
        $bank_accounts = BankAccounts::where('company_id', session('currentCompany'))
        ->pluck('account', 'id')
        ->toArray();

        $featured_account = BankAccounts::select('id')
        ->where('featured', 1)
        ->where('company_id', session('currentCompany'))
        ->first();

        //Métodos de pago:
        $payment_methods = PaymentMethod::where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Estados de pago:
        $payment_status = helpers::paymentStatus();

        $languages = $this->languages;

        $currency = $this->currency;

        return view('admin.effects.create', compact('header', 'bank_accounts', 'featured_account', 'payment_methods', 'payment_status', 'languages', 'currency'));
    }

    /**
     * 3. Guardar efecto.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/effects/create')
            ->withErrors($validator)
            ->withInput();
        }

        $date = $request->date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->date):$request->date:NULL;

        $customer_id = $request->customer_id? $request->customer_id:session('currentCompany');
        $provider_id = $request->provider_id? $request->provider_id:session('currentCompany');

        //Importe de las facturas:
        //10/09/23: las facturas se agregarán en la edición.
        //$amount = Invoice::whereIn('id', $request->invoices)->sum('amount_with_iva');

        //Descontamos paga y señales:
        // $pys = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
        // ->join('invoices', 'orders.id', '=', 'invoices.order_id')
        // ->whereIn('invoices.id', $request->invoices)
        // ->where('payments.is_pay_sign', 1)
        // ->sum('payments.amount');

        // $amount = $amount - $pys;
        $amount = 0;

        $customer_bank_account = $customer_id == session('currentCompany')? $request->my_bank_account:$request->bank_account;
        $provider_bank_account = $provider_id == session('currentCompany')? $request->my_bank_account:$request->bank_account;

        $is_group = $request->origin == 'group'? true:false;

        //Importe de efectos agrupados:
        if($is_group){
            $amount = Effect::whereIn('id', $request->effects)
            ->sum('amount');
        }

        $data = [
            'is_group' => $is_group,
            'parent' => NULL,
            'date' => $date,
            'provider_id' => $provider_id,
            'provider_bank_account_id' => $provider_bank_account,
            'customer_id' => $customer_id,
            'customer_bank_account_id' => $customer_bank_account,
            'invoice_id' => NULL,
            'serie_id' => $request->serie,
            'remittance_id' => NULL,
            'payment_method_id' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'amount' => $amount, 
            'observations' => $request->observations,
            'currency_id' => $request->currency_id,
            'created_by' => Auth::user()->id,
            'updated_by' => NULL,
            'status' => config('constants.EFFECT_STATUS_PENDENT_')
        ];
        $effect = Effect::setEffect($data); 

        //Si es agrupación, informamos los efectos agrupados:
        if($is_group){
            Effect::whereIn('id', $request->effects)
            ->update(['parent' => $effect->id]);
        }

        //Vinculamos las facturas al efecto:
        //10/09/23: las facturas se agregarán en la edición.
        // foreach($request->invoices as $inv){
        //  Invoice::where('id', $inv)->update(['effect_id' => $effect->id]);
        // }

        $msg = trans('textos.efecto_guardado');
        return redirect()->route('effects.edit', $effect)->with(compact('msg'));
    }

    /**
     * 4. Editar efecto.
     */
    public function edit(Effect $effect){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.efecto_editar',
            'module' => $this->module,
            'slug' => 'effects'
        ];

        $effect_status = helpers::effectsStatus();
        //Si el estado es distinto a pendiente, ya no permite volver al Estado de pendiente, por lo que se elimina este valor del array:
        if($effect->status != config('constants.EFFECT_STATUS_PENDENT_')){
            unset($effect_status[config('constants.EFFECT_STATUS_PENDENT_')]);
        }

        //Cuentas bancarias propias:
        $my_bank_accounts = BankAccounts::where('company_id', session('currentCompany'))
        ->pluck('account', 'id')
        ->toArray();

        //Cuentas bancarias del cliente o proveedor:
        $bank_account_company = $effect->company_id == $effect->provider_id? $effect->customer_id:$effect->provider_id;
        $bank_accounts = BankAccounts::where('company_id', $bank_account_company)
        ->pluck('account', 'id')
        ->toArray();

        //Métodos de pago:
        $payment_methods = PaymentMethod::where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Series:
        // $series = InvoiceSerie::select(DB::raw("CONCAT(code,'-',description) as cuenta"), 'id')
        // ->where('company_id', session('currentCompany'))
        // ->pluck('cuenta', 'id')
        // ->toArray();

        //Facturas:
        $invoices = Invoice::select('invoices.id', 'invoices.invoice_num', 'invoices.invoice_old_num', 'invoices.amount', 'invoices.amount_with_iva', 'invoices.fiscal_date', 'invoices.expiration_date', 'invoices.status', 'companies.name AS company')
        ->join('orders', 'invoices.order_id', '=', 'orders.id')
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->join('invoice_effects', 'invoices.id', '=', 'invoice_effects.invoice_id')
        ->where('invoice_effects.effect_id', $effect->id)
        ->get();

        $arr_invoices_in = [];
        foreach($invoices as $inv){
        array_push($arr_invoices_in, $inv->id);
        }

        //Facturas fuera de efecto:
        $obj_invoices_out = Invoice::select('invoices.id', 'invoices.invoice_num', 'invoice_old_num', 'companies.name')
        ->join('orders', 'invoices.order_id', '=', 'orders.id')
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->where('orders.provider_id', session('currentCompany'))
        ->whereNotIn('invoices.id', $arr_invoices_in)
        ->get();

        $invoices_out = [];
        foreach($obj_invoices_out as $oio){
        $num = $oio->invoice_num? $oio->invoice_num:($oio->invoice_old_num? $oio->invoice_old_num:$oio->id);
        $invoices_out[$oio->id] = $num.' - '.$oio->name;
        }

        //Estados de factura:
        $invoice_status = helpers::invoiceStatus();

        //Efectos de la agrupación:
        $son_effects = false;
        if($effect->is_group){
            $son_effects = Effect::select('effects.*', 'customers.name AS customer', 'providers.name AS provider')
            ->join('companies AS customers', 'effects.customer_id', '=', 'customers.id')
            ->join('companies AS providers', 'effects.provider_id', '=', 'providers.id')
            ->where('effects.parent', $effect->id)
            ->get();
        }

        //Estados de pago:
        $payment_status = helpers::paymentStatus();

        //Remesa:
        $remittance = $effect->remittance_id? Remittance::find($effect->remittance_id):false;

        //Cliente:
        $customer = Company::select('id', 'name', 'tradename')->find($effect->customer_id);

        //Autor:
        $created_by = User::select('name', 'surname')->find($effect->created_by);

        $languages = $this->languages;

        $currency = $this->currency;

        return view('admin.effects.edit', compact('header', 'effect', 'effect_status', 'my_bank_accounts', 'bank_accounts','payment_methods', 'invoices', 'invoices_out', 'invoice_status', 'son_effects', 'payment_status', 'remittance', 'customer', 'created_by', 'languages', 'currency'));
    }

    /**
     * 5. Actualizar efecto.
     * @param \App\Http\Requests\EffectUpdateRequest $request
     * @param \App\Models\Effect $effect
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Effect $effect){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/effects/'.$effect->id.'/edit')
            ->withErrors($validator)
            ->withInput();
        }

        //Formateando fechas:
        $date = $request->date? (session()->get('locale') != 'en') ? helpers::convertFecha($request->date) : $request->date : false;

        //Indicadores para actualización de las cuentas bancarias:
        $provider_bank_account_update = false;
        $customer_bank_account_update = false;

        if($request->my_bank_account){
            if($effect->customer_id == session('currentCompany')){
                $customer_bank_account = $request->my_bank_account; 
                $customer_bank_account_update = true;
            }

            if($effect->provider_id == session('currentCompany')){
                $provider_bank_account = $request->my_bank_account; 
                $provider_bank_account_update = true;
            }
        }
        if($request->bank_account){
            if($effect->customer_id != session('currentCompany')){
                $customer_bank_account = $request->bank_account;    
                $customer_bank_account_update = true;
            }

            if($effect->provider_id != session('currentCompany')){
                $provider_bank_account = $request->bank_account;    
                $provider_bank_account_update = true;
            }
        }
        
        if($request->amount){
            $effect->amount = $request->amount;
        }
        if($date){
            $effect->date = $date;
        }
        if($request->payment_method){
            $effect->payment_method_id = $request->payment_method;
        }
        if($provider_bank_account_update){
            $effect->provider_bank_account_id = $provider_bank_account;
        }
        if($customer_bank_account_update){
            $effect->customer_bank_account_id = $customer_bank_account;
        }
        if($request->payment_status){
            $effect->payment_status = $request->payment_status;
        }
        // if($request->serie){
        //     $effect->serie_id = $request->serie;
        // }
        if($request->status){
            $effect->status = $request->status;
        }
        $effect->observations = $request->observations;
        $effect->save();

        $msg = trans('textos.efecto_updated');
        return redirect()->route('effects.edit', $effect)->with(compact('msg'));
    }

    /**
     * 6. Eliminar efecto.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Effect $effect
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Effect $effect){
    //Desvinculamos facturas:
    Invoice::where('effect_id', $effect->id)
    ->update(['effect_id' => NULL]);

    $effect->delete();

    $msg = trans('textos.efecto_deleted');
    return redirect()->route('effects.index')->with(compact('msg'));
    }

    /**
     * 7. Actualizar estado de efecto.
     */
    public function status(Request $request, Effect $effect, $status){
        if ($request->ajax()) {
            $effect->status = $status;
            $effect->save();

            return response()->json(true);
        }
    }

    /**
     * 8. Vincular a remesa.
     */
    public function setRemittance(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
        //Indicamos el origen de la acción realizada para manejar el modal:
        $validator->getMessageBag()->add('accion', 'modal-add-remittance');

        return redirect('/admin/effects/index')
        ->withErrors($validator)
        ->withInput();
        }

        Effect::where('id', $request->effect_id)
        ->update(['remittance_id' => $request->remittance]);

        $msg = trans('textos.efecto_remesado');
        return redirect()->route('effects.index')->with(compact('msg'));
    }

    /**
     * 9. Vincular facturas.
     */
    public function setInvoices(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-invoices');

            return redirect('/admin/effects/'.$request->effect_id.'/edit')
            ->withErrors($validator)
            ->withInput();
        }

        foreach($request->invoices as $inv){
            Invoice::where('id', $inv)
            ->update(['effect_id' => $request->effect_id]);
        }

        $msg = trans('textos.facturas_agregadas');
        return redirect()->route('effects.edit', $request->effect_id)->with(compact('msg'));
    }

    /**
     * 10. Efectos agrupados.
     */
    public function groups($customer_id = false, $provider_id = false, $from = false, $to = false, $payment_method = false){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.efectos_agrupados',
            'module' => $this->module,
            'slug' => 'effects'
        ];

        //Cuentas bancarias propias:
        $my_bank_accounts = BankAccounts::select(DB::raw("CONCAT(entity,' ',iban,'-',office,'-',dc,'-',account) as cuenta"), 'id')
        ->where('company_id', session('currentCompany'))
        ->pluck('cuenta', 'id')
        ->toArray();

        $featured_account = BankAccounts::select('id')
        ->where('featured', 1)
        ->where('company_id', session('currentCompany'))
        ->first();

        //Cuentas bancarias del cliente o proveedor:
        $other_company = $customer_id? $customer_id:($provider_id? $provider_id:false);
        if($other_company){
            $bank_accounts = BankAccounts::select(DB::raw("CONCAT(entity,' ',iban,'-',office,'-',dc,'-',account) as cuenta"), 'id')
            ->where('company_id', $other_company)
            ->pluck('cuenta', 'id')
            ->toArray();
        }else{
            $bank_accounts = [];
        }

        //Métodos de pago:
        $payment_methods = PaymentMethod::where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Series:
        // $series = InvoiceSerie::select(DB::raw("CONCAT(code,'-',description) as cuenta"), 'id')
        // ->where('company_id', session('currentCompany'))
        // ->pluck('cuenta', 'id')
        // ->toArray();

        //Estados de pago:
        $payment_status = helpers::paymentStatus();

        $languages = $this->languages;

        $currency = $this->currency;

        return view('admin.effects.groups', compact('header', 'provider_id', 'customer_id', 'from', 'to', 'payment_method', 'my_bank_accounts', 'featured_account', 'bank_accounts', 'payment_methods', 'payment_status', 'languages', 'currency'));
    }

    /**
     * 10.1. Listado de efectos agrupados para DataTable.
     */
    public function getEffectsGroupsDT($customer_id = false, $provider_id = false, $from = false, $to = false, $payment_method = false){
        
        
        //PENDIENTE DE DESARROLLO.
    }

    /**
     * 11. Filtro para agrupaciones de efectos.
     */
    public function groupsFilter(Request $request){
        //dd($request->all());
        //Si no se informa de proveedor, éste es la empresa en sesión:
        $provider_id = $request->provider_id ? $request->provider_id : session('currentCompany');
        $customer_id = $request->customer_id ? $request->customer_id : session('currentCompany');

        //Sólo podemos tomar un criterio, nunca ambos:
        if($request->customer_id){
            $provider_id = session('currentCompany');
        }elseif($request->provider_id){
            $customer_id = session('currentCompany');
        }

        $from = $request->from? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:'x';
        $to = $request->to? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:'x';

        return redirect()->route('effects.groups', [$customer_id, $provider_id, $from, $to, $request->payment_method]);
    }

    /**
     * 12. Efectos por cuenta contable.
     */
    public function byAccountingAccount(Request $request, AccountingAccount $accounting_account){
        if($request->ajax()){
            $data = Effect::select('effects.id', 'effects.amount', 'effects.status')
            ->where('effects.company_id', $accounting_account->company_id)
            ->where('effects.status', config('contants.EFFECT_STATUS_PENDENT_'))
            ->get();

            return response()->json($data);
        }
    }
}
