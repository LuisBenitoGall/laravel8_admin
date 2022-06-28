<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'budgets';

    protected $dates = ['deleted_at'];

    public function shared(){
        return $this->hasOne(BudgetOrderSharedFields::class);
    }
}
