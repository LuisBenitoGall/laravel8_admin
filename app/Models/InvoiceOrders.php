<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Registro de los pedidos en facturas de agrupaciones de pedidos.

class InvoiceOrders extends Model{
    use HasFactory;

    protected $table = 'invoice_orders';
}
