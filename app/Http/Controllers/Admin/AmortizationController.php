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
use App\Models\AccountingAccount;
use App\Models\Amortization;
use App\Models\AmortizationLine;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\User;

class AmortizationController extends Controller{
    /**
     * 1. Relación de amortizaciones.
     * 1.1. Listado de amortizaciones por empresa para DataTable.
     * 2. Formulario nueva amortización.
     * 3. Guardar nueva amortización.
     * 4. Editar amortización.
     * 5. Actualizar amortización.
     * 6. Nuevas amortizaciones.
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
        //Validación create:
        if(isset($data['submit_save'])) {
            return Validator::make($data, [
                'description' => 'required',
                'amortization_account' => 'required|integer',
                'amortization_dota' => 'required|integer',
                'amount' => 'required'
            ]);

        //Validación update:
        } elseif (isset($data['submit_update'])) {
            return Validator::make($data, [
                'description' => 'required',
                'amortization_account' => 'required|integer',
                'amortization_dota' => 'required|integer',
                'amount' => 'required'
            ]);
        }
    }

    /**
     * 1. Relación de amortizaciones.
     */
    public function index(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.amortizaciones',
            'module' => $this->module,
            'slug' => 'amortizations'
        ];  

        $company = Company::find(session('currentCompany'));

        return view('admin.amortizations.index')->with(compact('header', 'company'));  
    }

    /**
     * 1.1. Listado de amortizaciones por empresa para DataTable.
     */
    public function getAmortizationsDT(){
        $data = Amortization::where('company_id', session('currentCompany'));

        return DataTables::of($data)
        ->addColumn('date', function ($row){
            return Carbon::parse($row->date)->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('amount', function ($row) {
            return $row->amount.$this->currency->symbol;
        })
        ->addColumn('amortization_pendent', function ($row){
            return $row->amortization_pendent;
        })
        ->addColumn('status', function ($row){ 
            if($row->status == 'A'){
                return '<span class="text-success" rel="tooltip" title="Amortización pendiente"><i class="fa fa-lock-open"></i></span>';
            }else{
                return '<span class="text-danger" rel="tooltip" title="Amortización cerrada"><i class="fa fa-lock"></i></span>';    
            }
        })
        ->addColumn('acciones', function ($row){
            return view('admin.amortizations.partials.dt-actions', compact('row'));
        })
        ->rawColumns(['status'])
        ->make(true);
    }

    /**
     * 2. Formulario nueva amortización.
     */
    public function create(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.amortizacion_nueva',
            'module' => $this->module,
            'slug' => 'amortizations'
        ]; 

        $currency = $this->currency; 

        //Cuentas contables:
        $accounting_accounts = AccountingAccount::select(DB::raw("CONCAT(account, '-', name) as cuenta"), 'id')
        ->where('company_id', session('currentCompany'))
        ->pluck('cuenta', 'id')
        ->toArray();

        $languages = $this->languages;

        return view('admin.amortizations.create')->with(compact('header', 'currency', 'accounting_accounts', 'languages'));    
    }

    /**
     * 3. Guardar nueva amortización.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect('/admin/amortizations/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Formateando fechas:
        $date = $request->date != '' ? (session()->get('locale') != 'en') ? helpers::convertFecha($request->date) : $request->date : NULL;

        $p = explode('-', $date);

        $a = new Amortization();
        $a->code = $request->code;
        $a->company_id = session('currentCompany');
        $a->description = $request->description;
        $a->permission = 'A';
        $a->date = $date;
        $a->amount = $request->amount;
        $a->amortization_account = $request->amortization_account;
        $a->amortization_dota = $request->amortization_dota;
        $a->year = $p[0];
        $a->month = $p[1];
        $a->amortization_pendent = $request->amount;
        $a->created_by = Auth::user()->id;
        $a->status = $request->status? 'A':'I';
        $a->save();

        $msg = trans('textos.amortizacion_creada');
        return redirect()->route('amortizations.edit', [$a->id, false])->with(compact('msg'));
    }

    /**
     * 4. Editar amortización.
     */
    public function edit(Amortization $amortization, $tab = false){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.amortizacion_editar',
            'module' => $this->module,
            'slug' => 'amortizations'
        ]; 

        $this->newAmortizations();

        $currency = $this->currency; 

        //Cuentas contables:
        $accounting_accounts = AccountingAccount::select(DB::raw("CONCAT(account, '-', name) as cuenta"), 'id')
        ->where('company_id', session('currentCompany'))
        ->pluck('cuenta', 'id')
        ->toArray();

        //Importe amortizado:
        $amortized = $amortization->linesAmount();

        //Autor:
        $author = $amortization->user;

        $languages = $this->languages;

        return view('admin.amortizations.edit')->with(compact('header', 'amortization', 'tab', 'currency', 'accounting_accounts', 'amortized', 'author', 'languages'));        
    }

    /**
     * 4.1. Líneas de amortización para DataTable.
     */
    public function getAmortizationItemsDT($amortization_id){
        $data = AmortizationLine::where('amortization_id', $amortization_id);

        return DataTables::of($data)
        ->addColumn('date', function ($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]); 
        })
        ->addColumn('amount', function ($row) {
            return $row->amount.$this->currency->symbol;
        })
        ->addColumn('percent', function ($row){
            return $row->percent.'%';
        })   
        ->addColumn('acciones', function ($row){
            return view('admin.amortizations.partials.dt-actions-items', compact('row'));
        })
        ->make(true);
    }

    /**
     * 5. Actualizar amortización.
     */
    public function update(Request $request, Amortization $amortization){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/amortizations/'.$amortization->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        //Formateando fechas:
        $date = $request->date != '' ? (session()->get('locale') != 'en') ? helpers::convertFecha($request->date) : $request->date : NULL;

        $amortization->code = $request->code;
        $amortization->description = $request->description;
        $amortization->date = $date;
        $amortization->amount = $request->amount;
        $amortization->amortization_account = $request->amortization_account;
        $amortization->amortization_dota = $request->amortization_dota;
        //$amortization->year = 
        //$amortization->month = 
        $amortization->amortization_pendent = $request->amount;
        $amortization->created_by = Auth::user()->id;
        $amortization->status = $request->status;
        //$amortization->deletion_date = 
        $amortization->save();

        $msg = trans('textos.amortizacion_updated');
        return redirect()->route('amortizations.edit', [$amortization->id, false])->with(compact('msg')); 
    }

    /**
     * 6. Nuevas amortizaciones.
     *
     * Método para utilizar con el cron de fin de año para actualización de amortizaciones.
     */
    public function newAmortizations(){
        $data = Amortization::select('id', 'amount', 'date', 'amortization_pendent', 'modify_date', 'status', 'deletion_date')
        ->where('status', 'A')
        ->where('amortization_pendent', '>', 0.01)
        ->orderBy('id', 'ASC')
        ->get();

        foreach($data as $row){
            if($row->amount > 0){
                $amount = $row->percent > 0? ($row->amount * $row->percent) / 100:'0';
                $amount = round($amount, 4);

                if($amount > $row->amortization_pendent){
                    $amount = $row->amortization_pendent;
                }

                //Primera línea de la amortización. Se calcula tanto por el porcentaje como por el período transcurrido durante el primer año y toma el período proporcional:
                $n_lines = AmortizationLine::select('id')->where('amortization_id', $row->id)->get();

                if(!$n_lines->count()){
                    //Día del año de la fecha de inicio de la amortización:
                    $any_day = date("z", strtotime($row->date));
                    //Número de días del año:
                    $days_in_year = date('L', strtotime($row->date))? 366:365;
                    //Días restantes para concluir el año:
                    $rest_days = $days_in_year - $any_day;

                    $amount = round(($rest_days * $amount) / $days_in_year, 4);
                }

                $line = new AmortizationLine();
                $line->amortization_id = $row->id;
                $line->amount = $amount;
                $line->percent = '0';
                $line->save();

                //Actualizando importe pendiente en la amortización.
                $row->updatePendentAmount();

                //Importe pendiente:
                $pendent = $row->amount - $row->amortization_pendent - $amount;

                $row->modify_date = date('Y-m-d');
                //Si el importe pendiente actualizado es inferior a 0.01, se da la amortización por cerrada.
                if($pendent <= 0.01){
                    $row->deletion_date = date('Y-m-d');
                    $row->status = 'I';
                }
                $row->save();
            }
        }
    }
}
