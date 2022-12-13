<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

//Models:
use App\Models\TempExportOrders;

class OrdersExport implements FromQuery, WithHeadings, ShouldAutoSize, WithColumnFormatting{
	use Exportable;

	private $temp_num;

	public function __construct($temp_num){
		$this->temp_num = $temp_num;
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query(){
        $data = TempExportOrders::query()
        ->select('order_date', 'estimated_date', 'delivery_date', 'order_num', 'order_old', 'order_title', 'order_status', 'order_provider_manager', 'order_customer', 'order_customer_manager', 'order_business_area', 'order_cost_center', 'order_tipology', 'order_price', 'order_qty', 'order_discount', 'order_amount')
        ->where('temp_num', $this->temp_num);

        return $data;
    }

    /**
     * Cabeceras de columna.
     */
    public function headings():array{
    	return['fecha del pedido', 'fecha estimada', 'fecha entrega', 'número pedido', 'número antiguo', 'título del pedido', 'estado', 'owner proveedor', 'empresa', 'owner cliente', 'área de negocio', 'área terapéutica', 'tipología', 'precio', 'cantidad', 'descuento (%)', 'importe'];
    }

    /**
     * Formato de columnas.
     */
    public function columnFormats(): array{
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }
    
}
