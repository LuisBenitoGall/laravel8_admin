<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProviders extends Model{
    /**
     * 1. Clientes por empresa.
     * 2. Proveedores por empresa.
     * 3. Relación cliente - proveedor.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customer_providers';    

    protected $dates = ['deleted_at'];

    protected $fillable = ['customer_id', 'provider_id', 'status'];

    /**
     * 1. Clientes por empresa.
     */
    public static function getCompanyCustomers($id){
        $data = CustomerProviders::select('companies.id', 'companies.name', 'customer_providers.budget_form_id')
        ->join('companies', 'customer_providers.customer_id', '=', 'companies.id')
        ->where('customer_providers.provider_id', $id)
        ->where('companies.id', '!=', $id)
        ->get();    

        return $data;
    }

    /**
     * 2. Proveedores por empresa.
     */
    public static function getCompanyProviders($id){
        $data = CustomerProviders::select('companies.id', 'companies.name')
        ->join('companies', 'customer_providers.provider_id', '=', 'companies.id')
        ->where('customer_providers.customer_id', $id)
        ->get();    

        return $data;
    }

    /**
     * 3. Relación cliente - proveedor.
     */
    public static function customerProvider($customer, $provider){
        $data = CustomerProviders::where('customer_id', $customer)
        ->where('provider_id', $provider)
        ->where('status', 1)
        ->first();

        return $data;
    }
}
