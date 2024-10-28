<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributes extends Model{
    use HasFactory;

    protected $table = 'product_attributes';

    protected $fillable = ['product_id', 'attribute_id', 'value', 'value_in_description'];
}
