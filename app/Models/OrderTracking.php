<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTracking extends Model{
    /**
     * 1. Guardar item de tracking. 
     */

    use HasFactory;
    use SoftDeletes; //Implementamos borrado lógico

    protected $dates = ['deleted_at']; //Columna de borrado lógico  

    protected $table = 'order_trackings';   

    /**
     * 1. Guardar item de tracking. 
     */
    public static function storeTrack($data){
        //No repetimos estado. Último track recibido:
        $last = OrderTracking::select('status')
        ->where('order_id', $data['order_id'])
        ->orderBy('created_at', 'DESC')
        ->first();
        
        if(!$last || $last->status != $data['status']){
            $track = new OrderTracking();
            $track->order_id = $data['order_id'];
            $track->user_id = $data['user_id'];
            $track->status = $data['status'];
            $track->save();
        }
    }
}
