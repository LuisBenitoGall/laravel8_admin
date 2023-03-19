<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SatPatterns extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    protected $table = 'sat_patterns';
    protected $casts = [
        'yearly_reset' => 'boolean',
    ];

    public function sats(){
        return $this->hasMany(Sat::class,'sat_pattern_id');
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }
}
