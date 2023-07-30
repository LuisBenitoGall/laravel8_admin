<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizEditionAnswers extends Model{
    use HasFactory;

    protected $table = 'quiz_edition_answers';

    protected $fillable = [
        'quiz_edition_id', 'quiz_edition_question_id', 'answer', 'user_id', 'team_id', 'answer_value'
    ];
}
