<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempImportBalances extends Model{
    use HasFactory;

    protected $table = 'temp_import_balances'; 

    protected $fillable = [
        'user_id', 'company_id', 'accounting_account_id', 'debit', 'credit', 'type', 'currency_id'
    ];
}
