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
     * 4. Centro de coste.
     * 5. Número de variantes.
     * 6. Atributos.
     * 7. Obtener valores de atributos como un string.
     * 8. Obtener la imagen destacada del producto.
     */
     
    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $table = 'products';
    
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
        $q = Product::select('products.id', 'products.name', 'products.ref', 'products.price', 'products.iva', 'products.calculated_by_percent', 'products.translations', 'products.description', 'products.on_sale', 'products.cost_center_id', 'products.batch', 'products.status', 'cost_centers.name AS cost_center',
            DB::raw('(SELECT doc FROM product_docs WHERE product_id = products.id AND featured = 1 LIMIT 1) AS Image'),
            DB::raw('(SELECT price FROM customer_products WHERE product_id = products.id AND provider_id = '.$company_id.' AND customer_id = '.$customer_id.') AS priceForCustomer')
        )
        ->leftJoin('cost_centers', 'products.cost_center_id', '=', 'cost_centers.id')
        ->where('products.company_id', $company_id);
        if($sale){
            $q->where('products.on_sale', 1);
        }
        if($status){
            $q->where('products.status', 1);
        }
        $q->orderBy('products.name', 'ASC');
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

    /**
     * 4. Centro de coste.
     */
    public function cost_center(){
        return $this->belongsTo(CostCenters::class);    
    }

    /**
     * 5. Número de variantes.
     */
    public function numVariants(){
        return $this->hasMany(Product::class, 'variant_of')->count();    
    }

    /**
     * 6. Atributos.
     */
    public function attributes(){
        return $this->belongsToMany(Attribute::class, 'product_attributes');    
    }

    /**
     * 7. Obtener valores de atributos como un string.
     */
    public function attributeValuesAsString($concat = ', '){
        return $this->attributes()
        ->pluck('product_attributes.value') 
        ->filter(function($value) {
            return !is_null($value); 
        })
        ->implode($concat); // Concatenar los valores con el concatenador recibido
    }

    /**
     * 8. Obtener la imagen destacada del producto.
     */
    public function featuredImage(){
        return $this->docs() // Utiliza la relación docs
        ->where('type', 'like', 'image/%') // Filtra por tipo 'imagen'
        ->where('featured', 1) // Asegúrate de que sea la imagen destacada
        ->first(); // Obtiene el primer resultado
    }
}



