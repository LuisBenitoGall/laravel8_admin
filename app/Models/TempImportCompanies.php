<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempImportCompanies extends Model{
    use HasFactory;

    protected $table = 'temp_import_companies'; 

    protected $fillable = [
        'user_id', 'name', 'tradename', 'nif', 'address', 'cp', 'town', 'billing_comunication', 'pay_after_days', 'payday', 'payment_method', 'iban', 'concepto_general', 'cuenta_gastos'
    ]; 
}
