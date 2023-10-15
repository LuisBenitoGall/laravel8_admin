<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingAccountType extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'accounting_account_types';

    protected $dates = ['deleted_at'];

    protected $fillable = ['autoreference', 'code', 'name'];
}
