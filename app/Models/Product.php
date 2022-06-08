<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model{
    /**
     * 1. Documentos del producto. 
     * 2. Relación con empresa. 
     * 3. Productos por empresa.
     */
     
    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 
    
    protected $fillable = [
        'product', 'description', 'long_description', 'price',
    ];   

    /**
     * 1. Documentos del producto.
     */
    public function docs(){
        return $this->hasMany(ProductDocs::class);
    }

    /**
     * 2. Relación con empresa.
     */
    public function company(){
        return $this->belongsTo(Company::class);
    }

    /**
     * 3. Productos por empresa.
     */
    public static function companyProducts($company_id, $sale = true, $status = true, $limit = false){
        $q = Product::select('id', 'name', 'price', 'iva', 'translations', 'description', 'on_sale', 'batch', 'status', DB::raw('(SELECT doc FROM product_docs WHERE product_id = products.id AND featured = 1 LIMIT 1) AS Image'))
        ->where('company_id', $company_id);
        if($sale){
            $q->where('on_sale', 1);
        }
        if($status){
            $q->where('status', 1);
        }
        $q->orderBy('name', 'ASC');
        if($limit){
            $q->limit($limit);
        }
        $data = $q->get();

        return $data;
    }  
}
