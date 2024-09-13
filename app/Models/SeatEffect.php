<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatEffect extends Model{
    use HasFactory;

    protected $table = 'seat_effects';

    protected $fillable = [
        'seat_id', 'effect_id'
    ];
}
