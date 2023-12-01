<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BudgetFormItems extends Model{
    /**
     * 1. Obtener items por bloque.
     * 2. Obtener bloques por presupuesto.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budget_form_items';

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    protected $fillable = ['name'];

    /**
     * 1. Obtener items por bloque.
     */
    public function getItems($id, $customer_id){
        $items = BudgetFormItems::select('products.name', 
            'products.price', 
            'products.iva', 
            'products.cost_center_id',
            'budget_form_items.id', 
            'budget_form_items.name AS concept', 
            'budget_form_items.form_id', 
            'budget_form_items.product_id', 
            'budget_form_items.discount', 
            'budget_form_items.qty_min', 
            'budget_form_items.recid', 
            'budget_form_items.calculated_by_percent',
            DB::raw('(SELECT price FROM customer_products WHERE product_id = products.id AND customer_id = '.$customer_id.') AS priceForCustomer')
        )
        ->leftJoin('products', 'budget_form_items.product_id', '=', 'products.id')
        ->where('budget_form_items.recid', $id)
        ->get();
        return $items;
    }

    /**
     * 2. Obtener bloques por presupuesto.
     */
    public static function getBudgetBlocks($form_id, $order_id){
        $data = BudgetFormItems::select('id', 'name', 'form_id', 'sort_block', 'cost_center_id')
        ->where('recid', '0')
        ->where(function($query) use($form_id, $order_id){
            $query->where('form_id', $form_id)
            ->orWhere('order_id', $order_id);
        })
        ->orderBy('sort_block', 'ASC')
        ->get();    

        return $data;
    }

  
}
