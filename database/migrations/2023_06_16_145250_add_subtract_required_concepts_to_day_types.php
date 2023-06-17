<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubtractRequiredConceptsToDayTypes extends Migration{
    /**
     * Run the migrations.
     * Determina si se deben descontar o no en la jornada los conceptos horarios obligatorios (desayuno, comida,...)
     *
     * @return void
     */
    public function up(){
        Schema::table('day_types', function (Blueprint $table) {
            $table->boolean('subtract_required_concept')->default(true)->after('criterion_id');
            $table->text('concepts_not_required')->nullable()->after('subtract_required_concept');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('day_types', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'day_types_not_included']);
        });
    }
}
