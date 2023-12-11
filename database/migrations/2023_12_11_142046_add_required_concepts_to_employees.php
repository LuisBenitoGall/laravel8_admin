<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequiredConceptsToEmployees extends Migration{
    /**
     * Run the migrations.
     *
     * Establece si se le aplican al empleado los conceptos horarios obligatorios.
     *
     * @return void
     */
    public function up(){
        Schema::table('employees', function (Blueprint $table){
            $table->boolean('required_concepts')->default(true)->after('time_control');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('employees', function (Blueprint $table){
            $table->dropColumn(['required_concepts']);
        });
    }
}
