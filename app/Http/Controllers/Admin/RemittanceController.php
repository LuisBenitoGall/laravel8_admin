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
                'bank_account' => 'required|integer'
            ]);

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
     */
    public function create(){
        $header = [
        'option' => $this->option,
        'suboption' => 'textos.remesa_nueva',
        'module' => $this->module,
        'slug' => 'remittances'
        ]; 

        //Cuentas bancarias:
        $bank_accounts = BankAccounts::where('company_id', session('currentCompany'))
        ->pluck('account', 'id')
        ->toArray();

        //Patrones:
        $patterns = RemittancePattern::where('status', 1)
        ->where('company_id', session('currentCompany'))
        ->pluck('name', 'id')
        ->toArray();

        //Tipos de efectos:
        $effect_types = helpers::effectTypes();

        //Efectos:
        $effects = Effect::getEffectsToRemittance();

        $languages = $this->languages;

        return view('admin.remittances.create')->with(compact('header', 'bank_accounts', 'patterns', 'effect_types', 'effects', 'languages'));
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
        'suboption' => 'textos.remesa_editar',
        'module' => $this->module,
        'slug' => 'remittances'
        ];

        //Importe de la remesa = suma importes de los efectos:
        $sum = Effect::where('remittance_id', $remittance->id)
        ->sum('amount');
        $remittance->amount = $sum;
        $remittance->save();

        $author = User::select('name', 'surname')->find($remittance->created_by);

        $selected_effects = Effect::select('effects.id', 'effects.effect_num', 'effects.parent', 'effects.customer_id', 'effects.date', 'effects.amount', 'effects.status', 'effects.type', 'bank_accounts.account', 'companies.name AS customer', 'currencies.symbol')
        ->join('bank_accounts', 'effects.provider_bank_account_id', '=', 'bank_accounts.id')
        ->join('companies', 'effects.customer_id', '=', 'companies.id')
        ->join('currencies', 'effects.currency_id', '=', 'currencies.id')
        ->where('remittance_id', $remittance->id)
        ->get();

        //Efectos:
        $effects = Effect::getEffectsToRemittance();

        //Tipos de efectos:
        $effect_types = helpers::effectTypes();

        //Cuentas bancarias:
        $bank_accounts = BankAccounts::where('company_id', session('currentCompany'))
        ->pluck('account', 'id')
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
        $remittance->amount = $request->amount;
        $remittance->send_date = $send_date;
        $remittance->paying_date = $paying_date;
        //$remittance->paying_by = $paying_by;
        $remittance->status = $request->status;
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
}
