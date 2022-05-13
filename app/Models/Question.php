<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model{
    /**
     * 1. Imágenes de la cuestión.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'questions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'company_id', 'name', 'slug', 'type', 'answer_type', 'status'
    ];

    /**
     * 1. Imágenes de la cuestión.
     */
    public function images(){
        return $this->hasMany(QuestionImages::class);
    }
}
