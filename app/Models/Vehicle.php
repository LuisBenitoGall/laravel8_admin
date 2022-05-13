<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model{
    /**
     * 1. Imágenes del vehículo.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'vehicles';

    protected $dates = ['deleted_at'];

    /**
     * 1. Imágenes del vehículo.
     */
    public function images(){
        return $this->hasMany(VehicleImages::class);
    }
}
