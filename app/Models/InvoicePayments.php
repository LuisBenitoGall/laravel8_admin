<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePayments extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoice_payments';

    protected $dates = ['deleted_at'];
}
