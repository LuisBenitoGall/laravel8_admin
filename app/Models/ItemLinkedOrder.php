<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemLinkedOrder extends Model{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'item_linked_orders'; 

    protected $dates = ['deleted_at'];
}
