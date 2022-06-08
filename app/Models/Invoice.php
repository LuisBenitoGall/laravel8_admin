<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Helpers;

class Invoice extends Model{
    /**
     * 1. Calcular importe de factura.
     */

    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at']; 

    protected $table = 'invoices';

    /**
     * 1. Calcular importe de factura.
     */
    public function calculateAmount(){
        if($this->with_iva > 0){
            $percent = Helpers::percent($this->amount, $this->with_iva); 
            $iva_amount = $this->amount + $percent;
        }else{
            $iva_amount = $this->amount;    
        }

        $this->amount_with_iva = $iva_amount;
        $this->save();
    }
}
