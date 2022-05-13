<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPrevisions extends Model{
    /**
     * 1. Guardar previsión de facturación.
     */

    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $table = 'order_previsions'; 

    /**
     * 1. Guardar previsión de facturación.
     */
    public static function store($order_id, $year = false, $percent = 100){
        $op = new OrderPrevisions();
        $op->order_id = $order_id;
        $op->year = $year? $year:date('Y');
        $op->percent = $percent;
        $op->save();  
        
        return $op;  
    } 
}
