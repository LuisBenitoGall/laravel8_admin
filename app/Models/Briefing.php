<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Briefing extends Model{
    /**
     * 1. Creado por.
     * 2. Actualizado por.
     * 3. Cliente.
     * 4. Pedido.
     * 5. Plantilla.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'briefings';

    protected $dates = ['deleted_at'];

    /**
     * 1. Creado por.
     */
    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 2. Actualizado por.
     */
    public function updatedBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 3. Cliente.
     */
    public function customer(){
        return $this->belongsTo(Company::class, 'customer_id');
    }

    /**
     * 4. Pedido.
     */
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * 5. Plantilla.
     */
    public function template(){
        return $this->belongsTo(BriefingTemplate::class, 'template_id');    
    }
}
