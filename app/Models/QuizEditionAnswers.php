<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizEditionAnswers extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'quiz_edition_answers';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'quiz_edition_question_id', 'answer_id', 'team_id', 'answer_value'
    ];
}
