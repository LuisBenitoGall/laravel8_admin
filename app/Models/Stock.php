<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model{
    /**
     * 1. Añadir o restar stock.
     */

    use HasFactory;

    protected $table = 'stocks';

    /**
     * 1. Añadir o restar stock.
     */
    public static function movStock($data){
        $stock = new Stock();
        $stock->product_id = $data['product_id'];
        $stock->stock_mov_id = $data['stock_mov_id'];
        $stock->batch_id = $data['batch_id'];
        $stock->store_id = $data['store_id'];
        $stock->user_id = $data['user_id'];
        $stock->qty = $data['qty'];
        $stock->operation = $data['operation'];
        $stock->result = $data['result'];
        $stock->doc = isset($data['doc'])? $data['doc']:NULL;
        $stock->doc_num = isset($data['doc_num'])? $data['doc_num']:NULL;
        $stock->for_user = isset($data['for_user'])? $data['for_user']:NULL;
        $stock->returned_at = isset($data['returned_at'])? $data['returned_at']:NULL;
        $stock->product_serial_id = isset($data['product_serial_id'])? $data['product_serial_id']:NULL;
        $stock->observations = isset($data['observations'])? $data['observations']:NULL;
        $stock->save();

        return $stock;
    }
}
