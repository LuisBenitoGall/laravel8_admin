<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class SeatLines extends Model{
    /**
     * 1. Guardar línea.
     * 2. Balance por cuenta y año.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'seat_lines';

    /**
     * 1. Guardar línea.
     */
    public static function saveLine($data){
        $sl = new SeatLines();
        $sl->seat_id = $data['seat_id'];
        $sl->line_num = $data['line_num'];
        $sl->accounting_account_id = $data['accounting_account_id']; 
        $sl->debit = $data['debit'];
        $sl->credit = $data['credit']; 
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
}
