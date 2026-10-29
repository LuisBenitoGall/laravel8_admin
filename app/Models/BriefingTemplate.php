<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BriefingTemplate extends Model{
    /**
     * 1. Creado por.
     * 2. Actualizado por.
     */

    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'briefing_templates';

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
}
