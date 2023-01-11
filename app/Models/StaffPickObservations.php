<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPickObservations extends Model
{
    use HasFactory;

    protected $table = 'staff_pick_observations';

    protected $casts = [
        'meeting_date' => 'date',
    ];

    /**
     * @return BelongsTo
     */
    public function staffPickCandidate(): BelongsTo
    {
        return $this->belongsTo(StaffPickCandidates::class, 'candidate_id');
    }
}
