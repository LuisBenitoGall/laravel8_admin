<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffPickCandidates extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'staff_pick_candidates';

    /**
     * @return BelongsTo
     */
    public function staffPick(): BelongsTo
    {
        return $this->belongsTo(StaffPick::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function staffPickObservations(): HasMany
    {
        return $this->hasMany(StaffPickObservations::class, 'candidate_id');
    }
}
