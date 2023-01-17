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
        //16/01/2023: los campos payday y condicion_pago son lo mismo. En el excel se mantiene la columna 'condicion pago' y en la BD 'payday'.
        //$payday = isset($row['dia_pago'])? $row['dia_pago']:NULL;
        //$condicion_pago = isset($row['condicion_pago'])? $row['condicion_pago']:NULL;
        $payday = isset($row['condicion_pago'])? $row['condicion_pago']:NULL;
        $payment_method = isset($row['forma_pago'])? $row['forma_pago']:NULL;
        $iban = isset($row['iban'])? $row['iban']:NULL;
        
        $concepto_general = isset($row['concepto_general'])? $row['concepto_general']:NULL;
        $cuenta_gastos = isset($row['cuenta_gastos'])? $row['cuenta_gastos']:NULL;

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
                //'pay_after_days' =>  $pay_after_days,
                'payday' => $payday,
                'payment_method' => $payment_method,
                'iban' => $iban,
                //'condicion_pago' => $condicion_pago,
                'concepto_general' => $concepto_general,
                'cuenta_gastos' => $cuenta_gastos
            ]); 
        }   
    }
}
