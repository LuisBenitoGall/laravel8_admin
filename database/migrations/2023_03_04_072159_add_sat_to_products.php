<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSatToProducts extends Migration{
    /**
     * Run the migrations.
     *
     * SAT: Servicio Asistencia Técnica. Vinculado a que se trate de un servicio, indica si inicia un proceso de SAT que implica a proveedores y otros productos.
     *
     * @return void
     */
    public function up(){
        Schema::table('products', function (Blueprint $table){
            $table->boolean('sat')->default(0)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('products', function (Blueprint $table){
            $table->dropColumn(['sat']);
        });
    }
}
