<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

//Models:
use App\Models\StockMovements;

class StockMovementsTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        /* Leyenda de sign:
        s -> suma,
        r -> resta,
        x -> traspaso 
        y -> devolución interna */

        $data = [
            ['name' => 'entrada inicial',
            'acronym' => 'ei',
            'sign' => 's',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => 1],
            ['name' => 'entrada manual',
            'acronym' => 'em',
            'sign' => 's',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'entrada contra pedido',
            'acronym' => 'ep',
            'sign' => 's',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'regularización positiva',
            'acronym' => 'rp',
            'sign' => 's',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'salida manual',
            'acronym' => 'sm',
            'sign' => 'r',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'salida contra pedido',
            'acronym' => 'sp',
            'sign' => 'r',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'regularización negativa',
            'acronym' => 'rn',
            'sign' => 'r',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'traspaso almacén',
            'acronym' => 'ta',
            'sign' => 'x',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'orden de trabajo',
            'acronym' => 'ot',
            'sign' => 'r',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'entrada por fabricación',
            'acronym' => 'ef',
            'sign' => 's',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'consumo interno',
            'acronym' => 'ci',
            'sign' => 'x',
            'basic' => true,
            'domestic_consumption' => true,
            'limit' => NULL],
            ['name' => 'devolución interna',
            'acronym' => 'di',
            'sign' => 'y',
            'basic' => true,
            'domestic_consumption' => true,
            'limit' => NULL],
            ['name' => 'devolución cliente',
            'acronym' => 'dc',
            'sign' => 's',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'compra',
            'acronym' => 'co',
            'sign' => 's',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL],
            ['name' => 'venta',
            'acronym' => 've',
            'sign' => 'r',
            'basic' => true,
            'domestic_consumption' => false,
            'limit' => NULL]
        ];

        foreach($data as $key => $value){
            StockMovements::create($value);
        }  
    }
}
