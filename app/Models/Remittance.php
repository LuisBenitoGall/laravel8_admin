<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remittance extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'remittances';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'customer_provider_id', 'remittance', 'mandate', 'start_date', 'end_date', 'status'

    ];
}
