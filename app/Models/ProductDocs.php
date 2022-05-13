<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDocs extends Model{
    /**
     * 1. Relación con products.
     */
    
    use HasFactory;

    /**
     * 1. Relación con products.
     */
    public function product(){
        return $this->belongsTo(Product::class);
    }       
}
