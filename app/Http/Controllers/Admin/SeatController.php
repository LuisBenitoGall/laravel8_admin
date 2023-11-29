<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\AccountingAccount;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Seat;
use App\Models\SeatLines;
use App\Models\SeatModel;
use App\Models\SeatModelLine;

class SeatController extends Controller{
    /**
     * 1. Relación de asientos contables.
     * 1.1. Listado de asientos contables para DataTable.
     * 2. Formulario nuevo asiento.
     * 3. Guardar nuevo asiento.
     * 4. Editar asiento contable.
     * 5. Actualizar asiento contable.
     * 6. Validación importes.
     * 7. Eliminar asiento contable.
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
                'seat_date' => 'required',
                'description' => 'required'
            ]);

        //Validación update:
        } elseif (isset($data['submit_update'])) {
            return Validator::make($data, [
                'seat_date' => 'required',
                'description' => 'required'
            ]);
        }
    }

    /**
     * 1. Relación de asientos contables.
     */
    public function index($year = false, $accounting_account_id = false, $from = false, $to = false){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.asientos_contables',
            'module' => $this->module,
            'slug' => 'seats'
        ];  

        $year = $year? $year:date('Y');

        $accounting_account = $accounting_account_id && $accounting_account_id != 'x'? AccountingAccount::select('id', 'account', 'name')->find($accounting_account_id):false;

        $company = Company::find(session('currentCompany'));

        $languages = $this->languages;

        return view('admin.seats.index')->with(compact('header', 'year', 'accounting_account', 'from', 'to', 'company', 'languages'));
    }

    /**
     * 1.1. Listado de asientos contables para DataTable.
     */
    public function getSeatsDT($from, $to, $accounting_account_id = false){
        $data = Seat::select('seats.*', 'companies.name AS other_company')
        ->leftJoin('companies', 'seats.company_target', '=', 'companies.id')
        ->leftJoin('seat_lines', 'seats.id', '=', 'seat_lines.seat_id')
        ->where('seats.company_id', session('currentCompany'))
        ->when($from && $to, function($q) use($from, $to){
            return $q->whereBetween('seats.created_at', [$from, $to]);
        })
        ->groupBy('seats.id')
        ->orderBy('seats.seat_date', 'DESC');  

        //Obtenemos la situación de los años comprendidos en el intervalo buscado:
        $p_year = explode('-', $from);
        $year_start = (int)$p_year[0];
        $years = [];
        while($year_start <= date('Y')){
            array_push($years, $year_start);
            $year_start++;
        }

        $open_balances = AccountingAccountYearBalance::join('accounting_accounts', 'accounting_account_year_balances.accounting_account_id', '=', 'accounting_accounts.id')
        ->where('accounting_accounts.company_id', session('currentCompany'))
        ->whereIn('accounting_account_year_balances.year', $years)
        ->where('accounting_account_year_balances.status', '0')
        ->groupBy('accounting_account_year_balances.year')
        ->pluck('accounting_account_year_balances.year')
        ->toArray();

        $currency = $this->currency;

        return DataTables::of($data)
        ->addColumn('num', function ($row){
            return $row->id;
        })
        ->addColumn('seat_date', function ($row){
            return Carbon::parse($row->seat_date)->format($this->languages[session()->get('locale')][4]);
        })
        // ->addColumn('amount', function ($row) use($currency){
        //     $amount = $row->linesAmount();
        //     return number_format($amount, 2).$currency->symbol;
        // })
        // ->addColumn('debit', function ($row){
            
        // })
        // ->addColumn('credit', function ($row){

        // })
        // ->addColumn('saldo', function ($row){

        // })
        ->addColumn('other_company', function($row){
            if($row->other_company){
                return $row->other_company;
            }else{
                return '';
            }
        })
        ->addColumn('acciones', function ($row) use($open_balances){
            return view('admin.seats.partials.dt-actions', compact('row', 'open_balances'));
        })
        ->make(true);  
    }    

    /**
     * 2. Formulario nuevo asiento.
     */
    public function create($model_id = false){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.asiento_nuevo',
            'module' => $this->module,
            'slug' => 'seats'
        ]; 

        $diaries = helpers::accountingDiaries();

        //$concepts = helpers::seatConcepts();
        $concepts = [];

        //Tipos de líneas de asientos contables:
        $seat_lines_types = helpers::seatLinesTypes();

        //Series:
        // $series = InvoiceSerie::select('code')
        // ->where('company_id', session('currentCompany'))
        // ->orderBy('code', 'ASC')
        // ->get();
        $series = [];

        $company = Company::select('id', 'name')->find(session('currentCompany'));

        //Modelos:
        $models = SeatModel::select('id', 'name')
        ->where('company_id', session('currentCompany'))
        ->get();

        $model = $model_id? SeatModel::find($model_id):false;
        $model_lines = $model_id? SeatModelLine::select('seat_model_lines.*', 'accounting_accounts.account', 'accounting_accounts.name')
            ->join('accounting_accounts', 'seat_model_lines.accounting_account_id', '=', 'accounting_accounts.id')
            ->where('seat_model_lines.model_id', $model_id)
            ->orderBy('seat_model_lines.line_num')
            ->get():false;

        $languages = $this->languages;

        $currency = $this->currency;

        return view('admin.seats.create')->with(compact('header', 'diaries', 'concepts', 'seat_lines_types', 'series', 'company', 'models', 'model', 'model_lines', 'languages', 'currency'));    
    }

    /**
     * 3. Guardar nuevo asiento.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/seats/create')
                ->withErrors($validator)
                ->withInput();
        }   

        //Validando importes del asiento:
        $amounts = $this->validateAmounts($request);

        //Si los importes difieren devolvemos error:
        if($amounts['debit'] != $amounts['credit']){
            $alert = 'Hay discrepancia entre el importe del asiento contable y sus líneas. Corrígelo antes de continuar.';
            return redirect()->route('seats.create')->with(compact('alert'));
            exit;
        }

        //Formateando fechas:
        $seat_date = $request->seat_date != '' ? (session()->get('locale') != 'en') ? helpers::convertFecha($request->seat_date) : $request->seat_date : NULL;
        $invoice_date = $request->invoice_date != '' ? (session()->get('locale') != 'en') ? helpers::convertFecha($request->invoice_date) : $request->invoice_date : NULL;

        //Líneas:
        $seat_lines = [];
        if($request->accounting_account_id){
            $seat_lines['accounting_account_id'] = $request->accounting_account_id;
            $seat_lines['type'] = $request->type;
            $seat_lines['debit'] = $request->debit;
            $seat_lines['credit'] = $request->credit;
        }

        //Generando asiento contable:
        $data = [
            'company_id' => session('currentCompany'),
            'seat_num' => '',
            'seat_type' => $request->seat_type,
            'seat_date' => $seat_date,
            'operation_type' => NULL,
            'diary' => $request->diary,
            'concept' => $request->concept,
            'description' => $request->description,
            'serie' => $request->serie,
            'doc_num' => $request->doc_num,
            'sort_num' => '',
            'company2_id' => $request->company_id,
            'mode' => $request->mode,
            'currency_id' => $this->currency->id,
            // 'base1' => $amounts['debe'],
            // 'base2' => 0,
            // 'base3' => 0,
            // 'iva_percent1' => 0,
            // 'iva_percent2' => 0,
            // 'iva_percent3' => 0,
            // 'iva_amount1' => 0,
            // 'iva_amount2' => 0,
            // 'iva_amount3' => 0,
            'invoice_id' => NULL,
            'invoice_date' => $invoice_date,
            'invoice_num' => $request->invoice_num,
            'invoice_amount' => NULL,
            'invoice_amount_iva' => NULL,
            'provider_doc' => $request->provider_doc,
            'cash_id' => NULL,
            'pay_sign' => $request->pay_sign,
            'accounting_sale_id' => NULL,
            'seat_lines' => $seat_lines
        ];
        $seat = Seat::saveSeat($data);

        $msg = trans('textos.asiento_creado');
        return redirect()->route('seats.edit', $seat)->with(compact('msg'));
    }

    /**
     * 4. Editar asiento contable.
     */
    public function edit(Seat $seat){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.asiento_editar',
            'module' => $this->module,
            'slug' => 'seats'
        ]; 

        $diaries = helpers::accountingDiaries();

        $concepts = helpers::seatConcepts();

        $lines = SeatLines::select('seat_lines.id', 'seat_lines.line_num', 'seat_lines.debit', 'seat_lines.credit', 'seat_lines.accounting_account_id', 'seat_lines.type', 'seat_lines.currency_id', 'accounting_accounts.account')
        ->leftJoin('accounting_accounts', 'seat_lines.accounting_account_id', '=', 'accounting_accounts.id')
        ->where('seat_lines.seat_id', $seat->id)
        ->orderBy('seat_lines.line_num', 'ASC')
        ->get();

        //Tipos de líneas de asientos contables:
        $seat_lines_types = helpers::seatLinesTypes();

        $company = Company::select('id', 'name')->find($seat->company_id);

        $languages = $this->languages;

        $currency = $this->currency;

        return view('admin.seats.edit')->with(compact('header', 'seat', 'diaries', 'concepts', 'lines', 'seat_lines_types', 'company', 'languages', 'currency'));    
    }

    /**
     * 5. Actualizar asiento contable.
     */
    public function update(Request $request, Seat $seat){
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect('/admin/seats/'.$seat->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        //dd($request->all());

        //Validando importes del asiento:
        $amounts = $this->validateAmounts($request);

        //Si los importes difieren devolvemos error:
        if($amounts['seat_amount'] != $amounts['lines_amount']){
            $alert = trans('textos.asiento_discrepancia');
            return redirect()->route('seats.create')->with(compact('alert'));
            exit;
        }

        //Formateando fechas:
        $seat_date = $request->seat_date != '' ? (session()->get('locale') != 'en') ? helpers::convertFecha($request->seat_date) : $request->seat_date : NULL;
        $invoice_date = $request->invoice_date != '' ? (session()->get('locale') != 'en') ? helpers::convertFecha($request->invoice_date) : $request->invoice_date : NULL;

        $seat->year = date('Y');
        $seat->seat_type = $request->seat_type;
        $seat->seat_date = $seat_date;
        $seat->operation_type = NULL;
        $seat->diary = $request->diary;
        $seat->concept = $request->concept;
        $seat->description = $request->description;
        $seat->serie = $request->serie;
        $seat->doc_num = $request->doc_num;
        $seat->company_target = $request->company_id;
        $seat->mode = $request->mode;
        $seat->currency_id = $this->currency->id;
        // $seat->base1 = $request->base1? $request->base1:0;
        // $seat->base2 = $request->base2? $request->base2:0;
        // $seat->base3 = $request->base3? $request->base3:0;
        // $seat->iva_percent1 = $request->iva_percent1? $request->iva_percent1:0;
        // $seat->iva_percent2 = $request->iva_percent2? $request->iva_percent2:0;
        // $seat->iva_percent3 = $request->iva_percent3? $request->iva_percent3:0;
        // $seat->iva_amount1 = $amounts['amount1'];
        // $seat->iva_amount2 = $amounts['amount2'];
        // $seat->iva_amount3 = $amounts['amount3'];
        $seat->updated_by = Auth::user()->id;
        $seat->invoice_date = $invoice_date;
        $seat->invoice_num = $request->invoice_num;
        $seat->provider_doc = $request->provider_doc;
        $seat->pay_sign = $request->pay_sign;
        $seat->save();

        //Líneas del asiento:
        //Eliminamos las líneas previas y se insertan por completo:
        SeatLines::where('seat_id', $seat->id)->delete();
        
        if($request->accounting_account){
            $i = 0;
            $line_num = 1;
            foreach($request->accounting_account as $aa){
                $sl = new SeatLines();
                $sl->seat_id = $seat->id;
                $sl->line_num = $line_num;
                $sl->accounting_account_id = $request->accounting_account[$i];
                $sl->debit = $request->debit[$i];
                $sl->credit = $request->credit[$i];
                $sl->code_auto = NULL;
                $sl->type = $request->type[$i]? $request->type[$i]:NULL;
                $sl->currency_id = $this->currency->id;
                $sl->save();

                $i++;
                $line_num++;
            }
        }

        $msg = trans('textos.asiento_updated');
        return redirect()->route('seats.edit', $seat)->with(compact('msg'));
    }

    /**
     * 6. Validación importes.
     */
    public function validateAmounts($request){
        $debit = 0;
        $credit = 0;

        foreach($request->debit as $d){
            if($d > 0){
                $debit = $debit + $d;
            }
        }
        foreach($request->credit as $h){
            if($h > 0){
                $credit = $credit + $h;
            }
        }

        $data['debit'] = $debit;
        $data['credit'] = $credit;
        return $data;
    }

    /**
     * 7. Eliminar asiento contable.
     */
    public function destroy(Seat $seat){
        //Líneas:
        SeatLines::where('seat_id', $seat->id)
        ->delete();

        $seat->delete();

        $msg = trans('textos.asiento_deleted');
        return redirect()->route('seats.index')->with(compact('msg'));
    }
}
