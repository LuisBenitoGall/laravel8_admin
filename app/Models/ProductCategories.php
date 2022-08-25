<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategories extends Model{
    /**
     * 1. Relación con products.
     */

    use HasFactory;

    protected $table = 'product_categories';

    /**
     * 1. Relación con products.
     */
    public function product(){
        return $this->belongsTo(Product::class);
    }     
}
