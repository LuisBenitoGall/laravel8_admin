<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetForms extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budget_forms';

    protected $dates = ['deleted_at'];
}
