<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketPatterns extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    protected $table = 'ticket_patterns';
    protected $casts = [
        'yearly_reset' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'provider_id');
    }
}
