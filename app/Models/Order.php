<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model{
    /**
     * 1. Documentos del pedido.
     */
    
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'orders'; 

    protected $dates = ['deleted_at'];

    /**
     * 1. Documentos del pedido.
     */
    public function docs(){
        return $this->hasMany(OrderDocs::class);
    }

    /**
     * 4. Pedidos por agrupación.
     */
    public static function getGroupOrders($group_id){
        $data = Order::select('order_group_orders.id AS ogo_id', 'orders.*')
        ->join('order_group_orders', 'order_group_orders.order_id', '=', 'orders.id')
        ->where('order_group_orders.og_id', $group_id)
        ->get();

        return $data;
    }
}
