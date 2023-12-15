<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class AccountingAccountYearBalance extends Model{
    /**
     * 1. Cálculo saldo por cuenta contable y año.
     * 2. Cálculo saldo por cuenta e intervalo de fechas.
     * 3. Guardar saldo.
     * 4. Actualizar saldo.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'accounting_account_year_balances';

    protected $dates = ['deleted_at'];

    /**
     * 1. Cálculo saldo por cuenta contable y año.
     */
    public static function getBalanceByInterval($accounting_account_id, $from, $to){
        //Líneas del año consultado:
        $data = SeatLines::select(
            DB::raw('sum(seat_lines.debit) as debit_'),
            DB::raw('sum(seat_lines.credit) as credit_')
        )
        ->join('seats', 'seat_lines.seat_id', '=', 'seats.id')
        ->where('seat_lines.accounting_account_id', $accounting_account_id)
        ->where('seats.seat_date', '>=', $from)
        ->where('seats.seat_date', '<=', $to)
        ->first();

        $p = explode('-', $from);
        $year = $p[0];

        //Obtenemos el saldo del año anterior:
        $prev_year = (int)$year - 1;
        $prev_balance = AccountingAccountYearBalance::select('id', 'balance', 'status')
        ->where('accounting_account_id', $accounting_account_id)
        ->where('year', $prev_year)
        ->first();
        $prevBalance = $prev_balance? $prev_balance->balance:'0';

        $balance = $prevBalance + ($data->debit_ - $data->credit_);

        return $balance;
    }

    /**
     * 2. Cálculo saldo por cuenta e intervalo de fechas.
     */
    public static function setBalanceByInterval($accounting_account_id, $from, $to){
        //Extraemos el saldo del año anterior al de la fecha de inicio:
        $p = explode('-', $from);
        $prev_year = $p[0];

        $last_balance = AccountingAccountYearBalance::setBalanceByYear($accounting_account_id, $prev_year);

        //La fecha de inicio se convierte a continuación en el 01-01 del año siguiente al de inicio:
        $next_year = (int)$prev_year + 1;
        $from = $next_year.'-01-01';
        //dd($prev_year, $last_balance, $from);

        $data = SeatLines::select(
            DB::raw('sum(seat_lines.debit) as debit_'),
            DB::raw('sum(seat_lines.credit) as credit_')
        )
        ->join('seats', 'seat_lines.seat_id', '=', 'seats.id')
        ->where('seat_lines.accounting_account_id', $accounting_account_id)
        ->where('seats.seat_date', '>=', $from)
        ->where('seats.seat_date', '<=', $to)
        ->first();  

        $balance = $data->debit_ - $data->credit_;  

        return $balance;
    }

    /**
     * 3. Guardar saldo.
     */
    public static function setBalanceByYear($accounting_account_id, $year){
        $from = $year.'-01-01';
        $to = $year.'-12-31';
        $balance = AccountingAccountYearBalance::getBalanceByInterval($accounting_account_id, $from, $to);

        $b = new AccountingAccountYearBalance();
        $b->accounting_account_id = $accounting_account_id;
        $b->year = $year;
        $b->balance = round($balance, 2); 
        $b->save();

        return $b;
    }

    /**
     * 4. Actualizar saldo.
     */
    public static function updateBalanceByYear($balance){
        $from = $balance->year.'-01-01';
        $to = $balance->year.'-12-31';
        $re_balance = AccountingAccountYearBalance::getBalanceByInterval($balance->accounting_account_id, $from, $to);

        $balance->balance = $re_balance;
        $balance->save();

        return $balance;
    }
}
