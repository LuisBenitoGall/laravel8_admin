<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;

//Models:
use App\Models\BankAccounts;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Effect;
use App\Models\Remittance;
use App\Models\RemittancePattern;
use App\Models\User;

class RemittanceController extends Controller{
    /**
     * 1. Relación de remesas por empresa emisora.
     * 1.1. Listado de remesas para DataTable.
     * 2. Formulario nueva remesa.
     * 3. Guardar remesa.
     * 4. Editar remesa.
     * 5. Actualizar remesa.
     * 6. Actualizar estado de remesa.
     * 7. Generar archivo XML.
     */

    private $option = 'remesas';
    private $module = 'accounting';
    private $languages;
    private $currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->currency = CompanySettings::co_currency(0);
        $this->middleware('remittance_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'type' => 'required|in:collection,payment',
                'bank_account' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'bank_account' => 'required|integer'
            ]);    
        }
    }

    /**
     * 1. Relación de remesas por empresa emisora.
     */
    public function index(){
    $header = [
    'option' => $this->option,
    'suboption' => 'textos.remesas',
    'module' => $this->module,
    'slug' => 'remittances'
    ];

    return view('admin.remittances.index', compact('header'));
    }

    /**
     * 1.1. Listado de remesas para DataTable.
     */
    public function getRemittancesDT(){
        $data = Remittance::select('remittances.*', 'bank_accounts.account', 'banks.tradename AS bank', 'users.name AS nombre', 'users.surname')
        ->join('bank_accounts', 'remittances.bank_account_id', '=', 'bank_accounts.id')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->join('users', 'remittances.created_by', '=', 'users.id')
        ->where('remittances.company_id', session('currentCompany'))
        ->orderBy('remittances.created_at', 'DESC');

        $estados = helpers::remittanceStatus();

        $currency = $this->currency;

        return DataTables::of($data)
        ->addColumn('num', function ($row) {
            $num = $row->remittance_num? $row->remittance_num:($row->remittance_old_num? $row->remittance_old_num:$row->id);
            return '<a href="/admin/remittances/'.$row->id.'/edit">'.$num.'</a>';
        })
        ->addColumn('type', function ($row) {
            $type = trans('textos.cobro');
            if($row->type == 2) $type = trans('textos.pago');
            return $type;
        })
        ->addColumn('created_at', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('send_date', function ($row) {
            return $row->send_date? Carbon::parse($row->send_date)->format($this->languages[session()->get('locale')][4]):'';
        })
        ->addColumn('paying_date', function ($row) {
            return $row->paying_date? Carbon::parse($row->paying_date)->format($this->languages[session()->get('locale')][4]):'';
        })
        ->addColumn('entity', function ($row) {
            return $row->bank;
        })
        ->addColumn('amount', function ($row) use($currency){
            return $row->amount.$currency->symbol;
        })
        ->addColumn('bank_account', function ($row) {
            return $row->account;
        })
        ->addColumn('created_by', function ($row) {
            return $row->nombre.' '.$row->surname;
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.invoices.partials.dt-status', compact('row', 'estados'));
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.remittances.partials.dt-actions', compact('row', 'estados'));
        })
        ->rawColumns(['num'])
        ->make(true);
    }

    /**
     * 2. Formulario nueva remesa.
     *
     * $type: determina si es para cobros (collection) o pagos (payment)
     */
    public function create($type){
        $header = [
        'option' => $this->option,
        'suboption' => $type == 'collection'? 'textos.remesa_nueva_cobros':'textos.remesa_nueva_pagos',
        'module' => $this->module,
        'slug' => 'remittances'
        ]; 

        //Cuentas bancarias:
        $bank_accounts = BankAccounts::select(DB::raw("CONCAT(banks.tradename,', ',bank_accounts.iban,'-',bank_accounts.office,'-',bank_accounts.dc,'-',bank_accounts.account) as cuenta"), 'bank_accounts.id')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->where('bank_accounts.company_id', session('currentCompany'))
        ->pluck('cuenta', 'bank_accounts.id')
        ->toArray();

        //Patrones:
        $patterns = RemittancePattern::where('status', 1)
        ->where('company_id', session('currentCompany'))
        ->pluck('name', 'id')
        ->toArray();

        //Tipos de efectos:
        $effect_types = helpers::effectTypes();

        //Efectos. Pasamos parámetros para indicar si son cobros o pagos.
        $effects = Effect::getEffectsToRemittance($type);

        $languages = $this->languages;

        return view('admin.remittances.create')->with(compact('type', 'header', 'bank_accounts', 'patterns', 'effect_types', 'effects', 'languages'));
    }

    /**
     * 3. Guardar remesa.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/remittances/create')
                ->withErrors($validator)
                ->withInput();
        } 

        $r = new Remittance();
        $r->company_id = session('currentCompany');
        $r->type = $request->type == 'collection'? 1:2;         //1: cobros;  2: pagos
        $r->bank_account_id = $request->bank_account;
        $r->created_by = Auth::user()->id;
        $r->save();

        //Numeración de la remesa:
        $remittance_arr = RemittancePattern::saveRemittanceNum($r, session('currentCompany'), $request->pattern_id);

        //Vincular efectos a remesa:
        if(is_array($request->effects) && count($request->effects)){
            foreach($request->effects as $e){
                Effect::where('id', $e)
                ->whereNull('remittance_id')        //Aseguramos que el efecto no haya sido incluido en otra remesa durante el intervalo del proceso.
                ->update(['remittance_id' => $r->id]);
            }
        }

        $msg = trans('textos.remesa_creada');
        return redirect()->route('remittances.edit', [$r->id, false])->with(compact('msg'));
    }

    /**
     * 4. Editar remesa.
     */
    public function edit(Remittance $remittance){
        $header = [
        'option' => $this->option,
        'suboption' => $remittance->type == 1? 'textos.remesa_editar_cobro':'textos.remesa_editar_pago',
        'module' => $this->module,
        'slug' => 'remittances'
        ];

        //Importe de la remesa = suma importes de los efectos:
        $sum = Effect::where('remittance_id', $remittance->id)
        ->sum('amount');
        $remittance->amount = $sum;
        $remittance->save();

        $author = User::select('name', 'surname')->find($remittance->created_by);

        $type = $remittance->type == 1? 'collection':'payment';

        $selected_effects = Effect::select('effects.id', 'effects.effect_num', 'effects.parent', 'effects.customer_id', 'effects.date', 'effects.amount', 'effects.status', 'effects.type', 'bank_accounts.account', 'banks.name AS bank', 'companies.name AS company', 'currencies.symbol')
        ->join('bank_accounts', 'effects.provider_bank_account_id', '=', 'bank_accounts.id')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->when($type, function($q) use($type){
            //Cobros:
            if($type == 'collection'){
                return $q->join('companies', 'effects.customer_id', '=', 'companies.id');
            }else{
                return $q->join('companies', 'effects.provider_id', '=', 'companies.id');
            }
        })
        ->join('currencies', 'effects.currency_id', '=', 'currencies.id')
        ->where('remittance_id', $remittance->id)
        ->get();

        //Efectos:
        $effects = Effect::getEffectsToRemittance($type);

        //Tipos de efectos:
        $effect_types = helpers::effectTypes();

        //Cuentas bancarias:
        $bank_accounts = BankAccounts::select(DB::raw("CONCAT(banks.tradename,', ',bank_accounts.iban,'-',bank_accounts.office,'-',bank_accounts.dc,'-',bank_accounts.account) as cuenta"), 'bank_accounts.id')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->where('bank_accounts.company_id', session('currentCompany'))
        ->pluck('cuenta', 'bank_accounts.id')
        ->toArray();

        //Patrones:
        $patterns = RemittancePattern::where('status', 1)
        ->where('company_id', session('currentCompany'))
        ->pluck('name', 'id')
        ->toArray();

        $estados = helpers::remittanceStatus();

        $languages = $this->languages;

        $currency = $this->currency;

        return view('admin.remittances.edit')->with(compact('header', 'remittance', 'author', 'selected_effects', 'effect_types', 'effects', 'bank_accounts', 'patterns', 'estados', 'languages', 'currency'));
    }

    /**
     * 5. Actualizar remesa.
     */
    public function update(Request $request, Remittance $remittance){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/remittances/'.$remittance->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        } 

        //Número de remesa:
        $patternNum = [];
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('remittance', $request->pattern_id);
        }

        //Formateando fechas:
        $send_date = $request->send_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->send_date):$request->send_date:NULL;
        $paying_date = $request->paying_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->paying_date):$request->paying_date:NULL;

        //Actualizando datos:
        $remittance->bank_account_id = $request->bank_account;
        if($request->pattern_id){
            if(!empty($patternNum)){
                $remittance->remittance_num = $patternNum['pattern_num'];  
                $remittance->num_cardinal = $patternNum['num']; 
            }            
            $remittance->pattern_id = $request->pattern_id;
        }
        $remittance->remittance_old_num = $request->remittance_old_num;
        $remittance->amount = $request->amount? $request->amount:'0';
        $remittance->send_date = $send_date;
        $remittance->paying_date = $paying_date;
        //$remittance->paying_by = $paying_by;
        $remittance->status = $request->status? $request->status:config('constants.REMITTANCE_STATUS_OPEN_');
        $remittance->save();

        //Vincular efectos a remesa. Eliminamos previamente todos los vínculos existentes:
        Effect::where('remittance_id', $remittance->id)
            ->update(['remittance_id' => NULL]);
        if(is_array($request->effects) && count($request->effects)){
            foreach($request->effects as $e){
                Effect::where('id', $e)
                ->update(['remittance_id' => $remittance->id]);
            }
        }

        $msg = trans('textos.remesa_updated');
        return redirect()->route('remittances.edit', [$remittance, false])->with(compact('msg'));
    }

    /**
     * 6. Actualizar estado de remesa.
     */
    public function status(Request $request, Remittance $remittance, $status){
        if ($request->ajax()) {
            $remittance->status = $status;
            $remittance->save();

            return response()->json(true);
        }
    }

    /**
     * 7. Generar archivo XML.
     */
    public function generateXml(Remittance $remittance){
        //Efectos:
        $effects = Effect::select('effects.id', 'effects.effect_num', 'effects.amount', 'c.id AS customer_id', 'c.name AS customer', 'c.nif AS customer_nif', 'p.id AS provider_id', 'p.name AS provider', 'p.nif AS provider_nif', 'cb.account AS customer_account', 'pb.account AS provider_account')
        ->join('companies AS c', 'effects.customer_id', '=', 'c.id')
        ->join('companies AS p', 'effects.provider_id', '=', 'p.id')
        ->leftJoin('bank_accounts AS cb', 'effects.customer_bank_account_id', '=', 'cb.id')
        ->leftJoin('bank_accounts AS pb', 'effects.provider_bank_account_id', '=', 'pb.id')
        ->where('effects.remittance_id', $remittance->id)
        ->where('effects.status', 1)
        ->get();

        $data = [];
        $k = 1;
        foreach($effects as $ef){
            $data['efecto '.$k] = [
                "id" => $ef->id,
                "effect_num" => $ef->effect_num,
                "amount" => $ef->amount,
                "customer_id" => $ef->customer_id,
                "customer" => $ef->customer,
                "customer_nif" => $ef->customer_nif,
                "provider_id" => $ef->provider_id,
                "provider" => $ef->provider,
                "provider_nif" => $ef->provider_nif,
                "provider_account" => $ef->customer_account,
                "provider_account" => $ef->provider_account
            ];
            $k++;
        }

        $remittance_name = $remittance->remittance_num? $remittance->remittance_num:trans('textos.remesa').$remittance->id;
        $fileName = $remittance_name.'.xml';

        $remittance->doc = $fileName;
        $remittance->save();

        $xml = ArrayToXml::convert($data, trans('textos.remesa'));

        Storage::put('public/remittances/'.$fileName, $xml);

        $msg = trans('textos.xml_generado');
        return redirect()->route('remittances.edit', [$remittance, false])->with(compact('msg'));
    }
}
