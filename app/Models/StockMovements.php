<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovements extends Model{
    use HasFactory;
    protected $dates = ['deleted_at'];

    protected $table = 'stock_movements';

    protected $fillable = ['name', 'acronym', 'sign', 'basic', 'domestic_consumption', 'limit'];
}
