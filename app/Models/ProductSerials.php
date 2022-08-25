<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSerials extends Model{
    /**
     * 1. Usuario vinculado.
     */
    
    use HasFactory;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $table = 'product_serials';

    /**
     * 1. Usuario vinculado.
     */
    public function user(){
        $data = User::select('users.id', 'users.name', 'users.surname', 'stocks.operation')
        ->join('stocks', 'users.id', '=', 'stocks.for_user')
        ->join('product_serials', 'stocks.product_serial_id', '=', 'product_serials.id')
        ->where('product_serials.id', $this->id)
        ->orderBy('stocks.id', 'DESC')
        ->first();
        
        return $data;    
    } 
}
