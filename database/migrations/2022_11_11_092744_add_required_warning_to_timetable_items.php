<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequiredWarningToTimetableItems extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->time('required_warning')->nullable()->after('required');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('timetable_items', function (Blueprint $table){
            $table->dropColumn(['required_warning']);
        });
    }
}
