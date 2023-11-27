<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeatModelLine extends Model{
    /**
     * 1. Guardar línea.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'seat_model_lines';

    /**
     * 1. Guardar línea.
     */
    public static function saveLine($data){
        $l = new SeatModelLine();
        $l->model_id = $data['model_id'];
        $l->accounting_account_id = $data['accounting_account_id'];
        $l->line_num = $data['line_num'];
        $l->debe = $data['debe'];
        $l->haber = $data['haber'];
        $l->code_auto = $data['code_auto'];
        $l->type = $data['type'];
        $l->currency_id = $data['currency_id'];
        $l->save();

        return $l;
    }
}
