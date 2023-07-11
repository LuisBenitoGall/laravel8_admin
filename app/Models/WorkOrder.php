<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model{
    /**
     * 1. Lotes.
     */
    
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'work_orders'; 

    protected $dates = ['deleted_at'];

    /**
     * 1. Lotes.
     */
    public function batches(){
        return $this->hasMany(Batch::class, 'work_order_id');   
    }
}
