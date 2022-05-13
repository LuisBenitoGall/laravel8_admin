<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DayTypes extends Model{
    /**
     * 1. Obtener empresa.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'day_types';

    protected $fillable = [
        'type', 'days', 'company_id'
    ];

    protected $dates = ['deleted_at'];

    /**
     * 1. Obtener empresa.
     */
    public function company(){
        return $this->belongsTo(Company::class);
    }   
}
