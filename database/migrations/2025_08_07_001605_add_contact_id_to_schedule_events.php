<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactIdToScheduleEvents extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('schedule_events', function (Blueprint $table) {
            //FK:
            $table->unsignedBigInteger('contact_id')->nullable()->after('guests');
            $table->foreign('contact_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('schedule_events', function (Blueprint $table) {
            $table->dropColumn(['contact_id']);
        });
    }
}
