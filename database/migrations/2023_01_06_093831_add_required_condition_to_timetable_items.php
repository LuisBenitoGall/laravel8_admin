<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequiredConditionToTimetableItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->string('required_condition')->nullable()->after('required_warning');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->dropColumn(['required_condition']);
        });
    }
}
