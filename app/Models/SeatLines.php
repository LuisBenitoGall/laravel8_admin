<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SeatLines extends Model{
    /**
     * 1. Guardar línea.
     * 2. Balance por cuenta y año.
     * 3. Setear y obtener saldo por cuenta.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'seat_lines';

    /**
     * 1. Guardar línea.
     * DEBE: suma
     * HABER: resta
     */
    public static function saveLine($data){
        //Obtenemos saldo:
        $balance = self::accountBalance($data['accounting_account_id']);
        $balance = $balance + $data['debit'] - $data['credit'];

        $sl = new SeatLines();
        $sl->seat_id = $data['seat_id'];
        $sl->line_num = $data['line_num'];
        $sl->accounting_account_id = $data['accounting_account_id']; 
        $sl->debit = $data['debit'];
        $sl->credit = $data['credit']; 
        $sl->balance = $balance;
        $sl->code_auto = $data['code_auto'];
        $sl->type = $data['type'];
        $sl->currency_id = $data['currency_id']; 
        $sl->save();

        return $sl;
    }

    /**
     * 2. Balance por cuenta y año.
     */
    public function accountBalanceByYear($accounting_account_id, $year){
        return AccountingAccountYearBalance::where('accounting_account_id', $accounting_account_id)
        ->where('year', $year)
        ->first();
    }

    /**
     * 3. Setear y obtener saldo por cuenta.
     */
    public static function accountBalance($accounting_account_id){
        $prev_balance = SeatLines::select('balance')
        ->where('accounting_account_id', $accounting_account_id)
        ->orderBy('id', 'DESC')
        ->first();

        //Si no existe saldo lo calculamos a partir de las líneas anteriores:
        if(!$prev_balance){
            $lines = SeatLines::select(
                DB::raw('SUM(debit) as total_debit'), 
                DB::raw('SUM(credit) as total_credit')
            )
            ->where('accounting_account_id', $accounting_account_id)
            ->first();

            $balance = $lines->total_debit - $lines->total_credit;

        }else{
            $balance = $prev_balance->balance;
        }  

        return $balance;  
    }
}
