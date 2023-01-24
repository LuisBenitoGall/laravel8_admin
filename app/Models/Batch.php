<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Batch extends Model{
    /**
     * 1. Crear nuevo lote.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'batches';

    protected $dates = ['deleted_at'];

    /**
     * 1. Crear nuevo lote.
     */
    public function storeBatch($data){
        $b = new Batch();
        $b->product_id = $data->product_id;
        $b->work_order_id = $data->work_order_id;
        $b->name = $data->name;


        $b->save();

        return $b;    
    }
}
