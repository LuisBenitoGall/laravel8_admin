<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrders extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'delivery_orders';

    protected $dates = ['deleted_at'];
}
