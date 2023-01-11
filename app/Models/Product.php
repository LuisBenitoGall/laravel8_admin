<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Product extends Model{
    /**
     * 1. Documentos del producto. 
     * 2. Relación con empresa. 
     * 3. Productos por empresa.
     * 3.1. Productos por empresa con gestión de stock.
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
     *
     * $for_dt -> indica si es para DataTables.
     */
    public static function companyProducts($company_id, $customer_id, $sale = true, $status = true, $limit = false, $for_dt = false){
        $q = Product::select('id', 'name', 'price', 'iva', 'calculated_by_percent', 'translations', 'description', 'on_sale', 'batch', 'status', 
            DB::raw('(SELECT doc FROM product_docs WHERE product_id = products.id AND featured = 1 LIMIT 1) AS Image'),
            DB::raw('(SELECT price FROM customer_products WHERE product_id = products.id AND provider_id = '.$company_id.' AND customer_id = '.$customer_id.') AS priceForCustomer')
        )
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
        if($for_dt){
            return $q;
        }else{
            $data = $q->get();

            return $data;
        }
    }  

    /**
     * 3.1. Productos por empresa con gestión de stock.
     */ 
    
    public function stocks(){
        return $this->hasMany(Stock::class);
    }
}


