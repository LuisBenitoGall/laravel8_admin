<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

//Models:
use App\Models\TempImportCompanies;

class CompaniesImport implements ToModel, WithHeadingRow{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row){
        $name = isset($row['razon_social'])? $row['razon_social']:NULL;
        $tradename = isset($row['nombre_comercial'])? $row['nombre_comercial']:NULL;
        $nif = isset($row['nif'])? $row['nif']:NULL;
        $address = isset($row['direccion'])? $row['direccion']:NULL;
        $cp = isset($row['cp'])? $row['cp']:NULL;
        $town = isset($row['poblacion'])? $row['poblacion']:NULL;
        $billing_comunication = isset($row['forma_envio_facturacion'])? $row['forma_envio_facturacion']:NULL;
        $pay_after_days = isset($row['forma_pago'])? $row['forma_pago']:NULL;
        $payday = isset($row['dia_pago'])? $row['dia_pago']:NULL;

        if($name){
            return new TempImportCompanies([
                'user_id' => Auth::user()->id,
                'name' => $name,
                'tradename' => $tradename,
                'nif' => $nif,
                'address' => $address,
                'cp' => $cp,
                'town' => $town,
                'billing_comunication' => $billing_comunication,
                'pay_after_days' =>  $pay_after_days,
                'payday' => $payday 
            ]); 
        }   
    }
}
