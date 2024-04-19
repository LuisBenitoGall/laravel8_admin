<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCategory extends Model{
    use HasFactory;

    protected $table = 'order_categories';

    protected $fillable = ['order_id', 'category_id'];

    /**
     * 1. Relación con orders.
     */
    public function order(){
        return $this->belongsTo(Order::class);
    }     
}
