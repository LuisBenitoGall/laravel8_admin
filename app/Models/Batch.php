<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
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
    public static function saveBatch($data){
        $b = new Batch();
        $b->product_id = $data['product_id'];
        $b->work_order_id = $data['work_order_id'];
        $b->batch_num = $data['batch_num'];
        $b->num_cardinal = $data['num_cardinal'];
        $b->pattern_id = $data['pattern_id'];
        $b->deadline = $data['deadline'];
        $b->expiry_date = $data['expiry_date'];
        $b->initial_qty = $data['initial_qty'];
        $b->doc_num = $data['doc_num'];
        $b->doc = $data['doc'];
        $b->created_by = Auth::user()->id;
        $b->save();

        return $b;    
    }
}
