<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OrderProductionPrevision extends Model{
    /**
     * 1. Guardar previsión de producción.
     * 2. Setear tabla de order_previsions a order_production_previsions.
     */
    
    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $table = 'order_production_previsions'; 

    protected $fillable = ['order_id', 'year', 'month', 'amount', 'percent'];

    /**
     * 1. Guardar previsión de producción.
     */
    public static function store($order_id, $year = false, $percent = 100){
        if(!$year) $year = date('Y');

        $row = OrderProductionPrevision::firstOrCreate(
            ['order_id'=> $order_id],
            ['order_id' => $order_id, 'year' => $year, 'percent' => $percent]
        );
        
        return $row;  
    } 

    /**
     * 2. Setear tabla de order_previsions a order_production_previsions.
     */
    public static function copyRegisters(){
        $data = OrderProductionPrevision::all();

        if(!$data->count()){
            $previsions = OrderPrevisions::all();

            foreach($previsions as $p){
                OrderProductionPrevision::create([
                    'order_id' => $p->order_id,
                    'year' => $p->year,
                    'month' => $p->month,
                    'amount' => $p->amount,
                    'percent' => $p->percent,
                    'deleted_at' => $p->deleted_at
                ]);    
            }
        }
    }
}
