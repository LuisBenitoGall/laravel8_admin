<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizEditionQuestions extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'quiz_edition_questions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'quiz_edition_id', 'question_id'
    ];
}
