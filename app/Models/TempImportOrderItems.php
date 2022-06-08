<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempImportOrderItems extends Model{
    use HasFactory;

    protected $table = 'temp_import_order_items'; 

    protected $fillable = [
        'budget_order_id', 'ref', 'concept', 'qty', 'price', 'iva', 'discount', 'observations'
    ];
}
