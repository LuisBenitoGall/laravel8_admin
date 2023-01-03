<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFixedPriorityToTimetableItems extends Migration{
    /**
     * Run the migrations.
     *
     * Determina si el valor fijo de duración tiene o no prevalencia sobre otros criterios.
     *
     * @return void
     */
    public function up(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->boolean('fixed_priority')->default(false)->after('fixed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->dropColumn(['fixed_priority']);
        });
    }
}
