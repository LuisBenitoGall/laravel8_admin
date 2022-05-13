<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model{
    /**
     * 1. Obtener nombre de la categoría madre.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'categories'; 

    protected $dates = ['deleted_at'];

    /**
     * 1. Obtener nombre de la categoría madre.
     */
    public function getCategoryName($subcategory){
        $category = Category::select('name')->where('id', $subcategory)->first();
        return $category->name;
    }
}
