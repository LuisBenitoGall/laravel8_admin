<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchPatterns extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'batch_patterns';

    public function orders(): HasMany{
        return $this->hasMany(Order::class);
    }

    public function company(): BelongsTo{
        return $this->belongsTo(Company::class);
    }
}
