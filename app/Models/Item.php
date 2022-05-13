<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model{
    /**
     * 1. Líneas de pedido.
     */
     
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'items'; 

    protected $dates = ['deleted_at']; 

    protected $fillable = [
        'product_id', 'budget_order_id', 'budget_form_block_id', 'budget_form_item_id', 'concept', 'qty', 'price', 'item_no_price', 'iva', 'discount', 'calculated_by_percent', 'deposit_used', 'active', 'status', 'print', 'user_id'
    ];
}
