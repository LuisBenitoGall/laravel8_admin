<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\AccountingAccountYearBalance;
use App\Models\Seat;
use App\Models\SeatLines;

class SeatLinesController extends Controller{
    /**
     * 1. Líneas de asiento contable para DataTable.
     * 2. Líneas por asiento contable.
     * 3. Actualizar líneas por asiento contable.
     * 4. Eliminar línea de asiento.
     */
    
    private $option = 'contabilidad';
    private $module = 'accounting';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
    }

    /**
     * 1. Líneas de asiento contable para DataTable.
     */
    public function getLinesDT($accounting_account_id, $from, $to){
        $data = SeatLines::select('seat_lines.*', 'seats.seat_date', 'seats.description', 'seats.doc_num')
        ->join('seats', 'seat_lines.seat_id', '=', 'seats.id')
        ->where('seat_lines.accounting_account_id', $accounting_account_id)
        ->where('seats.seat_date', '>=', $from)
        ->where('seats.seat_date', '<=', $to)
        ->orderBy('seats.seat_date', 'ASC');

        return DataTables::of($data)
                // ->with('date', '')
                // ->with('seat', '')
                // ->with('description', '')
                // ->with('doc', '')
                // ->with('debit', '')
                // ->with('credit', '')
                // ->with('saldo', 'xxxxxxx')
                // ->with('acciones', '')
        ->addColumn('date', function ($row){
            return  $row->setRowId('id');
            //return Carbon::parse($row->seat_date)->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('seat', function ($row){
            return $row->seat_id;
        })
        ->addColumn('description', function ($row){
            return $row->description;
        })
        ->addColumn('doc', function ($row){
            return $row->doc_num;
        })
        ->addColumn('debit', function ($row){
            return $row->debit;
        })
        ->addColumn('credit', function ($row){
            return $row->credit;
        })
        ->addColumn('saldo', function ($row){
            //return $row->
        })
        ->addColumn('acciones', function ($row){
            return view('admin.seat_lines.partials.dt-actions', compact('row'));
        })
        ->make(true);  
    }

    /**
     * 2. Líneas por asiento contable.
     */
    public function getLines(Request $request, $seat_id){
        if($request->ajax()){
            $data = SeatLines::select('seat_lines.*', 'accounting_accounts.account', 'accounting_accounts.name', 'currencies.symbol')
            ->leftJoin('accounting_accounts', 'seat_lines.accounting_account_id', '=', 'accounting_accounts.id')
            ->leftJoin('currencies', 'seat_lines.currency_id', '=', 'currencies.id')
            ->where('seat_lines.seat_id', $seat_id)
            ->orderBy('seat_lines.line_num', 'ASC')
            ->get();

            return response()->json($data);
        }
    }

    /**
     * 3. Actualizar líneas por asiento contable.
     */
    public function update(Request $request, Seat $seat){
        //Comprobamos que la cuenta contable del extracto no esté cerrada:
        $balance = AccountingAccountYearBalance::where('accounting_account_id', $request->accounting_account_extracted)
        ->where('year', $seat->year)
        ->first();

        if($balance && $balance->status == 1){
            $alert = trans('textos.cuenta_contable_cerrada');
            return redirect()->back()->with(compact('alert'));
            exit;
        }

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

        //Validando importes:
        if($debit != $credit){
            $alert = trans('textos.asiento_no_valido');
            return redirect()->back()->with(compact('alert'));
            exit;
        }

        $i = 0;
        foreach($request->id as $id){
            if($request->accounting_account[$i] > 0){
                SeatLines::where('id', $id)
                ->update([
                    'accounting_account_id' => $request->accounting_account[$i],
                    'debit' => $request->debit[$i],
                    'credit' => $request->credit[$i]
                ]);
            }

            $i++;
        }

        //Recalculando saldo: si lo permite su status y es año vencido.
        if($balance && $balance->status == '0' && $seat->year < date('Y')){
            AccountingAccountYearBalance::updateBalanceByYear($balance);    
        }

        $msg = trans('textos.asiento_updated');
        return redirect()->back()->with(compact('msg'));
    }

    /**
     * 4. Eliminar línea de asiento.
     */
    public function destroy(SeatLines $line){
        $line->delete();

        //Scripts de contrapartidas.

        $msg = trans('textos.linea_deleted');
        return redirect()->route('seats.index')->with(compact('msg'));
    }
}
