<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequiredToTimetableItems extends Migration{
    /**
     * Run the migrations.
     *
     * Determina si el concepto es obligatorio en cada jornada. Su valor son los minutos que penalizan en caso de no haberse observado su registro diario.
     *
     * @return void
     */
    public function up(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->integer('required')->nullable()->after('reviewable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->dropColumn(['required']);
        });
    }
}
