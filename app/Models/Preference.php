<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model{
    /**
     * 1. Relación con users.
     */

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'url', 'user_id'
    ];  

    /**
     * 1. Relación con users.
     */
    public function user(){
        return $this->belongsTo(Users::class);
    }
}
