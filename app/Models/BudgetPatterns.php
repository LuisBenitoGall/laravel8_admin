<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetPatterns extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'budget_patterns';

    protected $casts = [
        'yearly_reset' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class,'budget_pattern_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
