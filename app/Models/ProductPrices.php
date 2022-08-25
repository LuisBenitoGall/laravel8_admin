<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPrices extends Model{
    /**
     * 1. Importación inicial desde la tabla products.
     */

    use HasFactory;
    use SoftDeletes; //Implementamos borrado lógico

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    protected $table = 'product_prices';
    
    protected $fillable = [
        'product_id', 'price'
    ];   

    /**
     * 1. Importación inicial desde la tabla products.
     */
    public static function importFromProducts(){
        //Truncate de la tabla:
        ProductPrices::truncate();

        $products = Product::select('id', 'price')->get();

        foreach($products as $p){
            if($p->price > 0){
                ProductPrices::create([
                    'product_id' => $p->id,
                    'price' => $p->price
                ]);  
            }  
        }
    }    
}
