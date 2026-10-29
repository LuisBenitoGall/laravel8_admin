<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tariff extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tariffs';

    protected $dates = ['deleted_at'];

    /**
     * 1. Productos excluidos de la tarifa.
     */
    

    /**
     * 1. Productos excluidos de la tarifa.
     */
    public function products_excluded(){
        return $this->hasMany(TariffProductsExcluded::class);
    }
}
