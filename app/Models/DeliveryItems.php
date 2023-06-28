<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DeliveryItems extends Model{
    /**
     * 1. Guardar línea de albarán.
     */

    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'delivery_items';

    /**
     * 1. Guardar línea de albarán.
     */
    public static function storeItem($data){
        if($data['product_id']){
            //Comprobamos el stock del producto.
            $product = Product::select('id', 'batch', 'stock_management')->find($data['product_id']);
            if($product && $product->stock_management){

                

                

            //Sin gestión de stocks, la cantidad es la recibida del pedido.
            }else{
                $qty = $data['qty'];    
            }
            
        }else{
            $qty = $data['qty'];
        }

        $i = new DeliveryItems();
        $i->delivery_id = $data['delivery_id'];
        $i->item_id = $data['item_id'];
        $i->qty = $qty;
        $i->user_id = Auth::user()->id;
        $i->save();
    }
}
