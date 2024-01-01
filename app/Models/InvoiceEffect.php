<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InvoiceEffect extends Pivot{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'invoice_effects';
}
