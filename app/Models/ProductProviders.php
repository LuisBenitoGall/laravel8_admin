<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductProviders extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_providers';

    protected $dates = ['deleted_at'];

    protected $fillable = ['customer_product_id', 'provider_product_id', 'provider_id', 'provider_ref', 'provider_product'];
}
