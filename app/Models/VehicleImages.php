<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleImages extends Model{
    /**
     * 1. Imágenes del vehículo.
     */
    
    use HasFactory;

    protected $table = 'vehicle_images';

    /**
     * 1. Imágenes del vehículo.
     */
    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }
}
