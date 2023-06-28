<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\helpers;

class Delivery extends Model{
    /**
     * 1. Importe de albarán.
     */

    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'deliveries';

    /**
     * 1. Importe de albarán.
     */
    public function getDeliveryAmount(){
        $items = DeliveryItems::select('items.price', 'items.discount', 'delivery_items.qty')
        ->join('items', 'delivery_items.item_id', '=', 'items.id')
        ->where('delivery_items.delivery_id', $this->id)
        ->get();

        $total_amount = 0;
        foreach($items as $i){
            $amount = $i->price * $i->qty;
            if($i->discount > 0){
                $discount = helpers::percent($amount, $i->discount);
                $amount = $amount - $discount;
            }

            $total_amount = $total_amount + $amount;
        }

        return $total_amount;
    }
}
