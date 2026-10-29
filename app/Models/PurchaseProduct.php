<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseProduct extends Model{
    /**
     * 1. Cuenta contable asociada.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'purchase_products';

    /**
     * 1. Cuenta contable asociada.
     */
    public function accountingAccount(){
        return $this->belongsTo(AccountingAccount::class, 'accounting_account_id', 'id');
    }
}
