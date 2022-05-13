<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionImages extends Model{
    /**
     * 1. Imágenes de la cuestión.
     */

    use HasFactory;

    protected $table = 'question_images';

    protected $fillable = ['image', 'question_id', 'featured', 'thumbnail'];

    /**
     * 1. Imágenes de la cuestión.
     */
    public function question(){
        return $this->belongsTo(Question::class);
    }
}
