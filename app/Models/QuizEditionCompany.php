<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizEditionCompany extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'quiz_edition_companies';

    protected $fillable = ['quiz_edition_id', 'company_id'];
}
