<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItems extends Model{
    /**
     * 1. Líneas de factura.
     * 2. Total líneas de factura por pedido.
     * 3. Importe de línea por factura.
     */
    
    use HasFactory;

    protected $table = 'invoice_items';
    
}
